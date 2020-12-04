<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat;

use Bitter\SimpleLiveChat\Enumeration\ChatBubbleAlignment;
use Bitter\SimpleLiveChat\Enumeration\ChatBubbleIcon;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Utility\Service\Identifier;
use JsonSerializable;

class Config implements ApplicationAwareInterface, JsonSerializable
{
    use ApplicationAwareTrait;

    protected $config;
    protected $packageService;
    /** @var Package */
    protected $pkg;
    protected $basicThumbnailer;
    protected $idHelper;

    public function __construct(
        Repository $config,
        PackageService $packageService,
        BasicThumbnailer $basicThumbnailer,
        Identifier $idHelper
    )
    {
        $this->config = $config;
        $this->packageService = $packageService;
        $this->pkg = $this->packageService->getByHandle("simple_live_chat");
        $this->basicThumbnailer = $basicThumbnailer;
        $this->idHelper = $idHelper;
    }

    public function getPort()
    {
        return (int)$this->config->get("simple_live_chat.port", 8080);
    }

    public function setPort($port)
    {
        $this->config->save("simple_live_chat.port", (int)$port);
    }

    public function getAdminSecret()
    {
        $adminSecret = (string)$this->config->get("simple_live_chat.admin_secret");

        if ($adminSecret === '') {
            $adminSecret = $this->idHelper->getString(128);
            $this->setAdminSecret($adminSecret);
        }

        return $adminSecret;
    }

    public function setAdminSecret($adminSecret)
    {
        $this->config->save("simple_live_chat.admin_secret", (string)$adminSecret);
    }

    public function getWelcomeMessage()
    {
        $defaultWelcomeMessage = t("Hello how can we help you?");

        return (string)$this->config->get("simple_live_chat.welcome_message", $defaultWelcomeMessage);
    }

    public function setWelcomeMessage($welcomeMessage)
    {
        $this->config->save("simple_live_chat.welcome_message", (string)$welcomeMessage);
    }

    public function getOfflineMessage()
    {
        $defaultOfflineMessage = t("We are sorry but currently all agents are offline.");

        return (string)$this->config->get("simple_live_chat.offline_message", $defaultOfflineMessage);
    }

    public function setOfflineMessage($offlineMessage)
    {
        $this->config->save("simple_live_chat.offline_message", (string)$offlineMessage);
    }

    public function getNotificationMailAddress()
    {
        return (string)$this->config->get("simple_live_chat.notification_mail_address");
    }

    public function setNotificationMailAddress($notificationMailAddress)
    {
        $this->config->save("simple_live_chat.notification_mail_address", (string)$notificationMailAddress);
    }

    public function getChatBubbleUsePopup()
    {
        return (bool)$this->config->get("simple_live_chat.chat_bubble.use_popup", true);
    }

    public function setChatBubbleUsePopup($chatBubbleUsePopup)
    {
        $this->config->save("simple_live_chat.chat_bubble.use_popup", (bool)$chatBubbleUsePopup);
    }

    public function getChatBubbleVisibility()
    {
        return (bool)$this->config->get("simple_live_chat.chat_bubble.visibility", true);
    }

    public function setChatBubbleVisibility($chatBubbleVisibility)
    {
        $this->config->save("simple_live_chat.chat_bubble.visibility", (bool)$chatBubbleVisibility);
    }

    public function getChatBubbleBackgroundColorNormal()
    {
        return (string)$this->config->get("simple_live_chat.chat_bubble.background_color_normal", '#75ca2a');
    }

    public function setChatBubbleBackgroundColorNormal($chatBubbleBackgroundColorNormal)
    {
        $this->config->save("simple_live_chat.chat_bubble.background_color_normal", (string)$chatBubbleBackgroundColorNormal);
    }

    public function getChatBubbleTextColorNormal()
    {
        return (string)$this->config->get("simple_live_chat.chat_bubble.text_color_normal", '#ffffff');
    }

    public function setChatBubbleTextColorNormal($chatBubbleTextColorNormal)
    {
        $this->config->save("simple_live_chat.chat_bubble.text_color_normal", (string)$chatBubbleTextColorNormal);
    }

    public function getChatBubbleBackgroundColorActive()
    {
        return (string)$this->config->get("simple_live_chat.chat_bubble.background_color_active", '#75ca2a');
    }

    public function setChatBubbleBackgroundColorActive($chatBubbleBackgroundColorActive)
    {
        $this->config->save("simple_live_chat.chat_bubble.background_color_active", (string)$chatBubbleBackgroundColorActive);
    }

    public function getChatBubbleTextColorActive()
    {
        return (string)$this->config->get("simple_live_chat.chat_bubble.text_color_active", '#ffffff');
    }

    public function setChatBubbleTextColorActive($chatBubbleTextColorActive)
    {
        $this->config->save("simple_live_chat.chat_bubble.text_color_active", (string)$chatBubbleTextColorActive);
    }

    public function getChatBubbleAlignment()
    {
        return (string)$this->config->get("simple_live_chat.chat_bubble.alignment", ChatBubbleAlignment::BOTTOM_RIGHT);
    }

    public function setChatBubbleAlignment($chatBubbleAlignment)
    {
        $this->config->save("simple_live_chat.chat_bubble.alignment", (string)$chatBubbleAlignment);
    }

    public function getChatBubbleIcon()
    {
        return (string)$this->config->get("simple_live_chat.chat_bubble.icon", ChatBubbleIcon::COMMENTS);
    }

    public function setChatBubbleIcon($chatBubbleIcon)
    {
        $this->config->save("simple_live_chat.chat_bubble.icon", (string)$chatBubbleIcon);
    }

    public function getChatBubbleSize()
    {
        return (int)$this->config->get("simple_live_chat.chat_bubble.size", 60);
    }

    public function setChatBubbleSize($chatBubbleSize)
    {
        $this->config->save("simple_live_chat.chat_bubble.size", (int)$chatBubbleSize);
    }

    public function getChatBubbleBorderRadius()
    {
        return (int)$this->config->get("simple_live_chat.chat_bubble.border_radius", 30);
    }

    public function setChatBubbleBorderRadius($chatBubbleBorderRadius)
    {
        $this->config->save("simple_live_chat.chat_bubble.border_radius", (int)$chatBubbleBorderRadius);
    }

    public function getChatBubbleChatPageId()
    {
        return (int)$this->config->get("simple_live_chat.chat_bubble.chat_page_id");
    }

    public function setChatBubbleChatPageId($chatBubbleChatPageId)
    {
        $this->config->save("simple_live_chat.chat_bubble.chat_page_id", (int)$chatBubbleChatPageId);
    }

    public function getProfilePictureFileId()
    {
        return (int)$this->config->get("simple_live_chat.profile_picture_file_id");
    }

    public function setProfilePictureFileId($profilePictureFileId)
    {
        $this->config->save("simple_live_chat.profile_picture_file_id", (int)$profilePictureFileId);
    }

    public function getDefaultProfilePicturePath()
    {
        return $this->pkg->getRelativePath() . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "blank_profile.jpg";
    }

    public function getProfilePicturePath()
    {
        $file = File::getByID($this->getProfilePictureFileId());

        if ($file instanceof FileEntity) {
            return $this->basicThumbnailer->getThumbnail($file, 450, 450, true)->src;
        }

        return $this->getDefaultProfilePicturePath();
    }

    public function getChatPopupFontFamily()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.font_family", "Arial");
    }

    public function setChatPopupFontFamily($fontFamily)
    {
        $this->config->save("simple_live_chat.chat_popup.font_family", (string)$fontFamily);
    }

    public function getChatPopupFontSize()
    {
        return (int)$this->config->get("simple_live_chat.chat_popup.font_size", "14");
    }

    public function setChatPopupFontSize($fontSize)
    {
        $this->config->save("simple_live_chat.chat_popup.font_size", (int)$fontSize);
    }

    public function getChatPopupChatHeight()
    {
        return (int)$this->config->get("simple_live_chat.chat_popup.chat_height", "350");
    }

    public function setChatPopupChatHeight($chatHeight)
    {
        $this->config->save("simple_live_chat.chat_popup.chat_height", (int)$chatHeight);
    }

    public function getChatPopupTextColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.text_color", "#646464");
    }

    public function setChatPopupTextColor($textColor)
    {
        $this->config->save("simple_live_chat.chat_popup.text_color", (string)$textColor);
    }

    public function getChatPopupButtonColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.button_color", "#646464");
    }

    public function setChatPopupButtonColor($buttonColor)
    {
        $this->config->save("simple_live_chat.chat_popup.button_color", (string)$buttonColor);
    }

    public function getChatPopupBorderColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.border_color", "#ececec");
    }

    public function setChatPopupBorderColor($borderColor)
    {
        $this->config->save("simple_live_chat.chat_popup.border_color", (string)$borderColor);
    }

    public function getChatPopupBackgroundColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.background_color", "#ffffff");
    }

    public function setChatPopupBackgroundColor($backgroundColor)
    {
        $this->config->save("simple_live_chat.chat_popup.background_color", (string)$backgroundColor);
    }

    public function getChatPopupTimeColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.time_color", "#747474");
    }

    public function setChatPopupTimeColor($timeColor)
    {
        $this->config->save("simple_live_chat.chat_popup.time_color", (string)$timeColor);
    }

    public function getChatPopupIncomingMessageBubbleBackgroundColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.incoming_message_bubble_background_color", "#75ca2a");
    }

    public function setChatPopupIncomingMessageBubbleBackgroundColor($incomingMessageBubbleBackgroundColor)
    {
        $this->config->save("simple_live_chat.chat_popup.incoming_message_bubble_background_color", (string)$incomingMessageBubbleBackgroundColor);
    }

    public function getChatPopupIncomingMessageBubbleTextColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.incoming_message_bubble_text_color", "#ffffff");
    }

    public function setChatPopupIncomingMessageBubbleTextColor($incomingMessageBubbleTextColor)
    {
        $this->config->save("simple_live_chat.chat_popup.incoming_message_bubble_text_color", (string)$incomingMessageBubbleTextColor);
    }

    public function getChatPopupOutgoingMessageBubbleBackgroundColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.outgoing_message_bubble_background_color", "#ebebeb");
    }

    public function setChatPopupOutgoingMessageBubbleBackgroundColor($outgoingMessageBubbleBackgroundColor)
    {
        $this->config->save("simple_live_chat.chat_popup.outgoing_message_bubble_background_color", (string)$outgoingMessageBubbleBackgroundColor);
    }

    public function getChatPopupOutgoingMessageBubbleTextColor()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.outgoing_message_bubble_text_color", "#646464");
    }

    public function setChatPopupOutgoingMessageBubbleTextColor($outgoingMessageBubbleTextColor)
    {
        $this->config->save("simple_live_chat.chat_popup.outgoing_message_bubble_text_color", (string)$outgoingMessageBubbleTextColor);
    }

    public function getChatPopupButtonHelpText()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.button_help_text", t('Click here or press enter to send the message.'));
    }

    public function setChatPopupButtonHelpText($buttonHelpText)
    {
        $this->config->save("simple_live_chat.chat_popup.button_help_text", (string)$buttonHelpText);
    }

    public function getChatPopupMessagePlaceholder()
    {
        return (string)$this->config->get("simple_live_chat.chat_popup.message_placeholder", t('Enter your message here...'));
    }

    public function setChatPopupMessagePlaceholder($messagePlaceholder)
    {
        $this->config->save("simple_live_chat.chat_popup.message_placeholder", (string)$messagePlaceholder);
    }

    public function jsonSerialize()
    {
        /** @noinspection HtmlUnknownTarget */
        return [
            "port" => $this->getPort(),
            "visitorProfilePicture" => $this->getDefaultProfilePicturePath(),
            "agentProfilePicture" => $this->getProfilePicturePath(),
            "messages" => [
                "serverOffline" => t(
                    "The server is not running. Click %s to start the server.",
                    sprintf(
                        "<a href=\"%s\" target=\"_blank\">%s</a>",
                        (string)Url::to("/dashboard/system/optimization/jobs"),
                        t("here")
                    )
                ),
                "loadingMessages" => t("Loading chat history..."),
                "noMessages" => t("No messages available.")
            ]
        ];
    }
}