<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleLiveChat\Controller\SinglePage\Dashboard\SimpleLiveChat;

use Bitter\SimpleLiveChat\Config;
use Bitter\SimpleLiveChat\Enumeration\ChatBubbleAlignment;
use Bitter\SimpleLiveChat\Enumeration\ChatBubbleIcon;
use Bitter\SimpleLiveChat\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;

class Settings extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;
    /** @var Config */
    protected $config;
    /** @var Validation */
    protected $formValidator;

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->request = $this->app->make(Request::class);
        $this->config = $this->app->make(Config::class);
        $this->formValidator = $this->app->make(Validation::class);
    }

    public function view()
    {
        $isRunning = is_resource(@fsockopen('127.0.0.1', $this->config->getPort()));

        $chatBubbleAlignments = [
            ChatBubbleAlignment::TOP_LEFT => t('Top Left'),
            ChatBubbleAlignment::TOP_RIGHT => t('Top Right'),
            ChatBubbleAlignment::BOTTOM_LEFT => t('Bottom Left'),
            ChatBubbleAlignment::BOTTOM_RIGHT => t('Bottom Right')
        ];

        $chatBubbleIcons = [
            ChatBubbleIcon::COMMENTING_OUTLINE => t('Commenting (Outline)'),
            ChatBubbleIcon::COMMENTING => t('Commenting'),
            ChatBubbleIcon::COMMENTS_OUTLINE => t('Comments (Outline)'),
            ChatBubbleIcon::COMMENTS => t('Comments'),
            ChatBubbleIcon::COMMENT_OUTLINE => t('Comment (Outline)'),
            ChatBubbleIcon::COMMENT => t('Comment')
        ];

        if ($this->request->getMethod() === 'POST') {
            $this->formValidator->setData($this->request->request->all());

            $this->formValidator->addRequiredToken("save_settings");
            $this->formValidator->addRequiredColor("chatBubbleBackgroundColorNormal");
            $this->formValidator->addRequiredColor("chatBubbleTextColorNormal");
            $this->formValidator->addRequiredColor("chatBubbleBackgroundColorActive");
            $this->formValidator->addRequiredColor("chatBubbleTextColorActive");
            $this->formValidator->addRequiredArray("chatBubbleAlignment", array_keys($chatBubbleAlignments));
            $this->formValidator->addInteger("chatBubbleSize", null, false);
            $this->formValidator->addInteger("chatBubbleBorderRadius", null, false);
            $this->formValidator->addRequiredPage("chatBubbleChatPageId");
            $this->formValidator->addRequiredArray("chatBubbleIcon", array_keys($chatBubbleIcons));
            $this->formValidator->addInteger("chatPopupFontSize");
            $this->formValidator->addInteger("chatPopupChatHeight");
            $this->formValidator->addRequiredColor("chatPopupTextColor");
            $this->formValidator->addRequiredColor("chatPopupButtonColor");
            $this->formValidator->addRequiredColor("chatPopupBorderColor");
            $this->formValidator->addRequiredColor("chatPopupBackgroundColor");
            $this->formValidator->addRequiredColor("chatPopupTimeColor");
            $this->formValidator->addRequiredColor("chatPopupIncomingMessageBubbleBackgroundColor");
            $this->formValidator->addRequiredColor("chatPopupIncomingMessageBubbleTextColor");
            $this->formValidator->addRequiredColor("chatPopupOutgoingMessageBubbleBackgroundColor");
            $this->formValidator->addRequiredColor("chatPopupOutgoingMessageBubbleTextColor");
            $this->formValidator->addRequired("chatPopupButtonHelpText");
            $this->formValidator->addRequired("chatPopupMessagePlaceholder");

            if (!$this->formValidator->test()) {
                $this->error = $this->formValidator->getError();
            }

            if (!$this->error->has()) {
                if ($isRunning && $this->request->request->has("port")) {
                    $this->config->setPort($this->request->request->get("port"));
                }

                $this->config->setWelcomeMessage($this->request->request->get("welcomeMessage"));
                $this->config->setOfflineMessage($this->request->request->get("offlineMessage"));
                $this->config->setNotificationMailAddress($this->request->request->get("notificationMailAddress"));
                $this->config->setChatBubbleVisibility($this->request->request->has("chatBubbleVisibility"));
                $this->config->setChatBubbleBackgroundColorNormal($this->request->request->get("chatBubbleBackgroundColorNormal"));
                $this->config->setChatBubbleTextColorNormal($this->request->request->get("chatBubbleTextColorNormal"));
                $this->config->setChatBubbleBackgroundColorActive($this->request->request->get("chatBubbleBackgroundColorActive"));
                $this->config->setChatBubbleTextColorActive($this->request->request->get("chatBubbleTextColorActive"));
                $this->config->setChatBubbleAlignment($this->request->request->get("chatBubbleAlignment"));
                $this->config->setChatBubbleSize($this->request->request->get("chatBubbleSize"));
                $this->config->setChatBubbleBorderRadius($this->request->request->get("chatBubbleBorderRadius"));
                $this->config->setChatBubbleChatPageId($this->request->request->get("chatBubbleChatPageId"));
                $this->config->setChatBubbleIcon($this->request->request->get("chatBubbleIcon"));
                $this->config->setProfilePictureFileId($this->request->request->get("profilePictureFileId"));
                $this->config->setChatBubbleUsePopup($this->request->request->has("chatBubbleUsePopup"));
                $this->config->setChatPopupFontSize($this->request->request->get("chatPopupFontSize"));
                $this->config->setChatPopupChatHeight($this->request->request->get("chatPopupChatHeight"));
                $this->config->setChatPopupTextColor($this->request->request->get("chatPopupTextColor"));
                $this->config->setChatPopupButtonColor($this->request->request->get("chatPopupButtonColor"));
                $this->config->setChatPopupBorderColor($this->request->request->get("chatPopupBorderColor"));
                $this->config->setChatPopupBackgroundColor($this->request->request->get("chatPopupBackgroundColor"));
                $this->config->setChatPopupTimeColor($this->request->request->get("chatPopupTimeColor"));
                $this->config->setChatPopupIncomingMessageBubbleBackgroundColor($this->request->request->get("chatPopupIncomingMessageBubbleBackgroundColor"));
                $this->config->setChatPopupIncomingMessageBubbleTextColor($this->request->request->get("chatPopupIncomingMessageBubbleTextColor"));
                $this->config->setChatPopupOutgoingMessageBubbleBackgroundColor($this->request->request->get("chatPopupOutgoingMessageBubbleBackgroundColor"));
                $this->config->setChatPopupOutgoingMessageBubbleTextColor($this->request->request->get("chatPopupOutgoingMessageBubbleTextColor"));
                $this->config->setChatPopupButtonHelpText($this->request->request->get("chatPopupButtonHelpText"));
                $this->config->setChatPopupMessagePlaceholder($this->request->request->get("chatPopupMessagePlaceholder"));
                $this->config->setChatPopupFontFamily($this->request->request->get("chatPopupFontFamily"));

                $this->set('success', t("The settings has been updated successfully."));
            }
        }

        $this->set('chatBubbleAlignments', $chatBubbleAlignments);
        $this->set('chatBubbleIcons', $chatBubbleIcons);

        $this->set('isRunning', $isRunning);
        $this->set('port', $this->config->getPort());
        $this->set('welcomeMessage', $this->config->getWelcomeMessage());
        $this->set('offlineMessage', $this->config->getOfflineMessage());
        $this->set('notificationMailAddress', $this->config->getNotificationMailAddress());
        $this->set('chatBubbleVisibility', $this->config->getChatBubbleVisibility());
        $this->set('chatBubbleBackgroundColorNormal', $this->config->getChatBubbleBackgroundColorNormal());
        $this->set('chatBubbleTextColorNormal', $this->config->getChatBubbleTextColorNormal());
        $this->set('chatBubbleBackgroundColorActive', $this->config->getChatBubbleBackgroundColorActive());
        $this->set('chatBubbleTextColorActive', $this->config->getChatBubbleTextColorActive());
        $this->set('chatBubbleAlignment', $this->config->getChatBubbleAlignment());
        $this->set('chatBubbleSize', $this->config->getChatBubbleSize());
        $this->set('chatBubbleBorderRadius', $this->config->getChatBubbleBorderRadius());
        $this->set('chatBubbleChatPageId', $this->config->getChatBubbleChatPageId());
        $this->set('chatBubbleIcon', $this->config->getChatBubbleIcon());
        $this->set('profilePictureFileId', $this->config->getProfilePictureFileId());
        $this->set('chatBubbleUsePopup', $this->config->getChatBubbleUsePopup());
        $this->set('chatPopupFontSize', $this->config->getChatPopupFontSize());
        $this->set('chatPopupChatHeight', $this->config->getChatPopupChatHeight());
        $this->set('chatPopupTextColor', $this->config->getChatPopupTextColor());
        $this->set('chatPopupButtonColor', $this->config->getChatPopupButtonColor());
        $this->set('chatPopupBorderColor', $this->config->getChatPopupBorderColor());
        $this->set('chatPopupBackgroundColor', $this->config->getChatPopupBackgroundColor());
        $this->set('chatPopupTimeColor', $this->config->getChatPopupTimeColor());
        $this->set('chatPopupIncomingMessageBubbleBackgroundColor', $this->config->getChatPopupIncomingMessageBubbleBackgroundColor());
        $this->set('chatPopupIncomingMessageBubbleTextColor', $this->config->getChatPopupIncomingMessageBubbleTextColor());
        $this->set('chatPopupOutgoingMessageBubbleBackgroundColor', $this->config->getChatPopupOutgoingMessageBubbleBackgroundColor());
        $this->set('chatPopupOutgoingMessageBubbleTextColor', $this->config->getChatPopupOutgoingMessageBubbleTextColor());
        $this->set('chatPopupButtonHelpText', $this->config->getChatPopupButtonHelpText());
        $this->set('chatPopupMessagePlaceholder', $this->config->getChatPopupMessagePlaceholder());
        $this->set('chatPopupFontFamily', $this->config->getChatPopupFontFamily());
    }

}
