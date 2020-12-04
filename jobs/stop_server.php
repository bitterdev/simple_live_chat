<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleLiveChat\Job;

use Bitter\SimpleLiveChat\Config;
use Concrete\Core\Http\Client\Client;
use Concrete\Core\Job\Job;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Exception;

/** @noinspection PhpUnused */
class StopServer extends Job
{
    public function getJobName()
    {
        return t('Stop Server');
    }

    public function getJobDescription()
    {
        return t('Stops the live chat server.');
    }

    /**
     * @return string
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public function run()
    {
        $permissionKey = Key::getByHandle("control_server");

        if ($permissionKey instanceof Key && $permissionKey->validate()) {
            $app = Application::getFacadeApplication();
            /** @var Client $client */
            $client = $app->make(Client::class);
            /** @var Config $config */
            $config = $app->make(Config::class);

            $payload = [];

            try {
                $response = $client
                    ->setUri((string)Url::to("/simple_live_chat/api/stop_server")->setQuery([
                        "secret" => $config->getAdminSecret()
                    ]))
                    ->setOptions([
                        'timeout' => 1
                    ])
                    ->send();

                $payload = json_decode($response->getBody());
            } catch (Exception $err) {
                // The time out exception is wanted here.
            }

            if ($payload->error) {
                foreach($payload->errors as $error) {
                    throw new Exception($error);
                }
            }

            return t("The server has been stopped successfully.");
        } else {
            throw new Exception(t("You don't have the permission to control the server."));
        }

    }

}
