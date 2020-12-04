<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleLiveChat;

use Bitter\SimpleLiveChat\Console\Command\StartServer;
use Bitter\SimpleLiveChat\Console\Command\StopServer;
use Bitter\SimpleLiveChat\Provider\ServiceProvider;
use Concrete\Core\Job\Job;
use Concrete\Core\Package\Package;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Exception;

class Controller extends Package implements ProviderAggregateInterface
{
    protected $pkgHandle = 'simple_live_chat';
    protected $pkgVersion = '1.0.0';
    protected $appVersionRequired = '8.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/SimpleLiveChat' => 'Bitter\SimpleLiveChat',
    ];

    public function getPackageDescription()
    {
        return t('Feature-rich self-hosted live chat solution that fully complies with GDPR and doesn\'t require any 3rd party services.');
    }

    public function getPackageName()
    {
        return t('Simple Live Chat');
    }

    public function getEntityManagerProvider()
    {
        return new StandardPackageProvider($this->app, $this, [
            'src/Bitter/SimpleLiveChat/Entity' => 'Bitter\SimpleLiveChat\Entity'
        ]);
    }

    public function on_start()
    {
        if ($this->app->isRunThroughCommandLineInterface()) {
            $console = $this->app->make('console');
            $console->add(new StartServer());
            $console->add(new StopServer());
        }

        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function testForUninstall()
    {
        $readyToUninstall = parent::testForUninstall();

        /** @var Job $job */
        $job = Job::getByHandle("stop_server");

        if (is_object($job)) {
            try {
                $job->run();
            } catch (Exception $err) {
                // Skip errors
            }
        }

        return $readyToUninstall;
    }

    public function install()
    {
        parent::install();

        $this->installContentFile('install.xml');

        /** @var Job $job */
        $job = Job::getByHandle("start_server");

        if (is_object($job)) {
            try {
                $job->run();
            } catch (Exception $err) {
                // Skip errors
            }
        }
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile('install.xml');
    }

}