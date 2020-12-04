<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Utility\Service\Identifier;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use function Ratchet\Client\connect;

class Server implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $config;
    protected $idHelper;
    protected $server;
    protected $socket;

    public function __construct(
        Config $config,
        Identifier $idHelper,
        Socket $socket
    )
    {
        $this->config = $config;
        $this->idHelper = $idHelper;
        $this->socket = $socket;
    }

    public function start()
    {
        $errorList = new ErrorList();

        if (!is_resource(@fsockopen('127.0.0.1', $this->config->getPort()))) {
            $this->config->setAdminSecret($this->idHelper->getString(128));
            $this->server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        $this->socket
                    )
                ),
                $this->config->getPort()
            );
            $this->server->run();
        } else {
            $errorList->add(t("The server is already running or the port is in use by another application."));
        }

        return $errorList;
    }

    public function stop()
    {
        $errorList = new ErrorList();

        if (is_resource(@fsockopen('127.0.0.1', $this->config->getPort()))) {
            if ($this->server instanceof IoServer) {
                $this->server->loop->stop();
            } else {
                $adminSecret = (string)$this->config->getAdminSecret();

                if (strlen($adminSecret) > 0) {
                    connect("ws://127.0.0.1:" . $this->config->getPort())->then(function ($conn) use ($adminSecret) {
                        $conn->send(json_encode([
                            "command" => "shutdownServer",
                            "secret" => $adminSecret
                        ]));
                    });
                } else {
                    $errorList->add(t("The admin secret is empty."));
                }
            }
        } else {
            $errorList->add(t("The server is not running."));
        }

        return $errorList;
    }
}