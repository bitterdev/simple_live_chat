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

/** @var string $chatHeight */
/** @var string $textColor */
/** @var string $buttonColor */
/** @var string $borderColor */
/** @var string $backgroundColor */
/** @var string $timeColor */
/** @var string $incomingMessageBubbleTextColor */
/** @var string $incomingMessageBubbleBackgroundColor */
/** @var string $outgoingMessageBubbleTextColor */
/** @var string $outgoingMessageBubbleBackgroundColor */
/** @var int $fontSize */
/** @var string $messagePlaceholder */
/** @var string $buttonHelpText */

/** @noinspection PhpUnhandledExceptionInspection */
View::element("simple_live_chat", [
    "adminMode" => false,
    "chatHeight" => $chatHeight,
    "textColor" => $textColor,
    "buttonColor" => $buttonColor,
    "borderColor" => $borderColor,
    "backgroundColor" => $backgroundColor,
    "timeColor" => $timeColor,
    "incomingMessageBubbleTextColor" => $incomingMessageBubbleTextColor,
    "incomingMessageBubbleBackgroundColor" => $incomingMessageBubbleBackgroundColor,
    "outgoingMessageBubbleTextColor" => $outgoingMessageBubbleTextColor,
    "outgoingMessageBubbleBackgroundColor" => $outgoingMessageBubbleBackgroundColor,
    "fontSize" => $fontSize,
    "messagePlaceholder" => $messagePlaceholder,
    "buttonHelpText" => $buttonHelpText
], "simple_live_chat");