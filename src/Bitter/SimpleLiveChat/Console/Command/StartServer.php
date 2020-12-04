<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat\Console\Command;

use Bitter\SimpleLiveChat\Server;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StartServer extends Command
{
    protected function configure()
    {
        $this
            ->setName('simple-live-chat:start-server')
            ->setDescription(t('Starts the live chat server.'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();

        /** @var Server $server */
        $server = $app->make(Server::class);

        $errorList = $server->start();

        $io = new SymfonyStyle($input, $output);

        if (!$errorList->has()) {
            $io->success(t("The server has been started successfully."));
        } else {
            foreach ($errorList->getList() as $error) {
                $io->error($error->getMessage());
            }
        }
    }
}
