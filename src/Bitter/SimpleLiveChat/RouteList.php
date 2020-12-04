<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->routes('api.php', 'simple_live_chat');

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\SimpleLiveChat\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/simple_live_chat')
            ->routes('dialogs/support.php', 'simple_live_chat');
    }
}