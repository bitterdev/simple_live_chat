<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\View\View;

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'simple_live_chat');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "simple_live_chat", "rateUrl" => "https://www.concrete5.org/marketplace/addons/simple-live-chat/reviews"], 'simple_live_chat');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "entity_designer"], 'simple_live_chat');

/** @noinspection PhpUnhandledExceptionInspection */
View::element("simple_live_chat", [
    "adminMode" => true,
    "chatHeight" => 350,
    "textColor" => "#646464",
    "buttonColor" => "#646464",
    "borderColor" => "#ececec",
    "backgroundColor" => "#ffffff",
    "timeColor" => "#747474",
    "incomingMessageBubbleTextColor" => "#ffffff",
    "incomingMessageBubbleBackgroundColor" => "#105192",
    "outgoingMessageBubbleTextColor" => "#646464",
    "outgoingMessageBubbleBackgroundColor" => "#ebebeb",
    "fontSize" => 14,
    "sidebarNormalBackgroundColor" => "#ffffff",
    "sidebarNormalTextColor" => "#646464",
    "sidebarActiveBackgroundColor" => "#105192",
    "sidebarActiveTextColor" => "#ffffff",
    "sidebarMessageCounterBackgroundColor" => "#ea0956",
    "sidebarMessageCounterTextColor" => "#ffffff",
    "messagePlaceholder" => t('Click here or press enter to send the message.'),
    "buttonHelpText" => t('Enter your message here...')
], "simple_live_chat");

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "simple_live_chat"], 'simple_live_chat');