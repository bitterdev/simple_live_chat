<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Routing\Router;

/** @var Router $router */

$router
    ->buildGroup()
    ->setPrefix("/simple_live_chat/api")
    ->setNamespace("Bitter\SimpleLiveChat\Controller")
    ->routes(function($groupRouter) {
        $groupRouter->get('/get_config', 'Api::getConfig');
        $groupRouter->get('/start_server', 'Api::startServer');
        $groupRouter->get('/stop_server', 'Api::stopServer');
        $groupRouter->get('/hide_reminder', 'Api::hideReminder');
        $groupRouter->get('/hide_did_you_know', 'Api::hideDidYouKnow');
        $groupRouter->get('/hide_license_check', 'Api::hideLicenseCheck');
    });