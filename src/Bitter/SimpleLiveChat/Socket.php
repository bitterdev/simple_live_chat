<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat;

use Bitter\SimpleLiveChat\Entity\Client as ClientEntity;
use Bitter\SimpleLiveChat\Entity\Message;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Utility\Service\Text;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Exception;
use DateTime;
use Concrete\Core\Mail\Service;

/* Push Notificaton add-on */

use paragraph1\phpFCM\Message as PushMessage;
use paragraph1\phpFCM\Notification as PushNotification;
use paragraph1\phpFCM\Recipient\Device as PushDevice;
use Bitter\PushNotifications\Settings as PushSettings;
use paragraph1\phpFCM\Client as PushClient;
use GuzzleHttp\Client as HttpClient;

class Socket implements MessageComponentInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $clients = [];
    protected $config;
    protected $entityManager;
    protected $textService;
    protected $packageService;
    protected $hasPushNotificationSupport;
    protected $mailService;

    public function __construct(
        Config $config,
        EntityManagerInterface $entityManager,
        Text $textService,
        PackageService $packageService,
        Service $mailService
    )
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->textService = $textService;
        $this->packageService = $packageService;
        $this->hasPushNotificationSupport = $this->packageService->getByHandle("push_notifications") !== null;
        $this->mailService = $mailService;
    }


    /**
     * @param Message $messageEntry
     */
    private function sendPushNotification(
        $messageEntry
    )
    {
        /*
         * Check if push notification add-on is available
         */

        if ($this->hasPushNotificationSupport) {

            $clientEntry = $this->entityManager->getRepository(ClientEntity::class)->findOneBy(["clientId" => $messageEntry->getTo()]);

            if ($clientEntry instanceof ClientEntity) {
                $settings = new PushSettings();
                $client = new PushClient();
                $client->setApiKey($settings->getServerKey());
                $client->injectHttpClient(new HttpClient());

                $notification = new PushNotification(
                    t("New message"),
                    $this->textService->shorten($messageEntry->getBody(), 128)
                );

                $notification->setClickAction($clientEntry->getChatUrl());

                $message = new PushMessage();

                $message
                    ->setNotification($notification)
                    ->addRecipient(new PushDevice($clientEntry->getPushToken()));

                $client->send($message);
            }
        }
    }

    public function onMessage(
        ConnectionInterface $from,
        MessageInterface $msg
    )
    {
        $payload = $msg->getPayload();

        $data = json_decode($payload);

        $isAdminMode = ($data->secret === $this->config->getAdminSecret());

        if ($data->clientId === 'admin' && !$isAdminMode) {
            $data->clientId = '';
        }

        switch ($data->command) {
            case "shutdownServer":
                if ($isAdminMode) {
                    /** @var Server $server */
                    $server = $this->app->make(Server::class);
                    $server->stop();
                }

                break;

            case "getMessages":
                $messages = [];

                $this->entityManager->clear();

                $queryBuilder = $this->entityManager->createQueryBuilder();

                if ($isAdminMode) {
                    if (isset($data->visitor)) {
                        $selectedVisitor = $data->visitor;

                        $query = $queryBuilder
                            ->select(['m'])
                            ->from(Message::class, 'm')
                            ->where($queryBuilder->expr()->orX(
                                $queryBuilder->expr()->eq('m.from', ':clientId'),
                                $queryBuilder->expr()->eq('m.to', ':clientId')
                            ))
                            ->setParameter('clientId', $selectedVisitor)
                            ->orderBy('m.timestamp', 'ASC')
                            ->getQuery();

                        $messages = $query->getResult();
                    }

                } else {
                    $query = $queryBuilder
                        ->select(['m'])
                        ->from(Message::class, 'm')
                        ->where($queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('m.from', ':clientId'),
                            $queryBuilder->expr()->eq('m.to', ':clientId')
                        ))
                        ->setParameter('clientId', $data->clientId)
                        ->orderBy('m.timestamp', 'ASC')
                        ->getQuery();

                    $messages = $query->getResult();

                    if (count($messages) === 0) {
                        $welcomeMessage = $this->config->getWelcomeMessage();

                        if (strlen($welcomeMessage) > 0) {
                            $messageEntry = new Message();

                            $messageEntry
                                ->setFrom('admin')
                                ->setTo($data->clientId)
                                ->setBody($welcomeMessage)
                                ->setTimestamp(new DateTime());

                            $this->entityManager->persist($messageEntry);
                            $this->entityManager->flush();

                            $messages[] = $messageEntry;
                        }
                    }
                }

                $from->send(json_encode([
                    "command" => "loadMessages",
                    "messages" => $messages
                ]));

                break;

            case "getVisitors":
                if ($isAdminMode) {
                    $sql = <<<EOT
SELECT 
	m2.from as clientId,
	(
		SELECT 
			m1.timestamp
		FROM 
			SimpleLiveChatMessage AS m1
		WHERE
			m1.from = m2.from
		ORDER BY
			m1.timestamp DESC
		LIMIT 1
	) AS lastTimestamp
FROM
	 SimpleLiveChatMessage AS m2
WHERE
    m2.from != 'admin'
GROUP BY
	m2.from
EOT;

                    $visitors = $this->entityManager->getConnection()->fetchAll($sql);

                    foreach ($visitors as $index => $row) {
                        $temp = new DateTime($row["lastTimestamp"]);
                        $visitors[$index]["lastTimestamp"] = $temp instanceof DateTime ? $temp->format("U") * 1000 : null;
                    }

                    $from->send(json_encode([
                        "command" => "loadVisitors",
                        "visitors" => $visitors
                    ]));
                }

                break;

            case "registerClient":
                $this->clients[$data->clientId] = $from;

                /** @var ConnectionInterface $receiver */
                $receiver = $this->clients['admin'];

                if (is_object($receiver) && $data->clientId !== 'admin') {
                    $receiver->send(json_encode([
                        "command" => "refreshVisitors"
                    ]));
                }

                /*
                 * Update the push token
                 */

                if (strlen($data->pushToken) > 0) {
                    $this->entityManager->clear();

                    $clientEntry = $this->entityManager->getRepository(ClientEntity::class)->findOneBy([
                        "clientId" => $data->clientId
                    ]);

                    if (!$clientEntry instanceof ClientEntity) {
                        $clientEntry = new ClientEntity();
                        $clientEntry->setClientId($data->clientId);
                    }

                    $clientEntry->setPushToken($data->pushToken);
                    $clientEntry->setChatUrl($data->chatUrl);

                    $this->entityManager->persist($clientEntry);
                    $this->entityManager->flush();
                }

                break;

            case "sendMessage":
                $messageEntry = new Message();
                $messageEntry
                    ->setBody($data->body)
                    ->setTimestamp(new DateTime());

                if ($isAdminMode) {
                    $messageEntry->setFrom('admin');
                    $messageEntry->setTo($data->to);
                } else {
                    $messageEntry->setFrom($data->from);
                    $messageEntry->setTo('admin');
                }

                $this->entityManager->persist($messageEntry);
                $this->entityManager->flush();

                /** @var ConnectionInterface $receiver */
                $receiver = $this->clients[$messageEntry->getTo()];

                if (is_object($receiver) && $receiver !== $from) {
                    $receiver->send(json_encode([
                        "command" => "loadMessage",
                        "message" => $messageEntry
                    ]));
                }

                $this->sendPushNotification($messageEntry);

                if ($data->clientId !== 'admin') {
                    /** @var ConnectionInterface $receiver */
                    $receiver = $this->clients['admin'];

                    if (is_object($receiver)) {
                        $receiver->send(json_encode([
                            "command" => "refreshVisitors"
                        ]));
                    } else {
                        $offlineMessageEntry = new Message();
                        $offlineMessageEntry
                            ->setFrom("admin")
                            ->setTo($data->clientId)
                            ->setBody($this->config->getOfflineMessage())
                            ->setTimestamp(new DateTime());

                        $this->entityManager->persist($offlineMessageEntry);
                        $this->entityManager->flush();

                        $from->send(json_encode([
                            "command" => "loadMessage",
                            "message" => $offlineMessageEntry
                        ]));

                        if (strlen($this->config->getNotificationMailAddress())> 0) {
                            $this->mailService->to($this->config->getNotificationMailAddress());
                            $this->mailService->load("offline_message", "simple_live_chat");

                            try {
                                $this->mailService->sendMail();
                            } catch (Exception $err) {
                                // Skip any errors
                            }
                        }
                    }
                }

                break;
        }
    }

    public function onClose(
        ConnectionInterface $conn
    )
    {
        if (($clientId = array_search($conn, $this->clients)) !== false) {
            unset($this->clients[$clientId]);
        }
    }

    public function onError(
        ConnectionInterface $conn,
        Exception $e
    )
    {
        $conn->close();
    }

    function onOpen(
        ConnectionInterface $conn
    )
    {
        // Do Nothing
    }
}