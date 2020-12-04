<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat\Provider;

use Bitter\SimpleLiveChat\Config;
use Bitter\SimpleLiveChat\Enumeration\ChatBubbleAlignment;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Foundation\Service\Provider;
use Bitter\SimpleLiveChat\RouteList;
use Bitter\SimpleLiveChat\Server;
use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Event;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Router;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\View\View;
use HtmlObject\Element;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->initializeAutoloader();
        $this->initializeSingletons();
        $this->initializeRoutes();
        $this->initializeAssets();
        $this->initializeChatButton();
    }

    private function initializeSingletons()
    {
        $this->app->singleton(Server::class);
    }

    private function initializeRoutes()
    {
        /** @var Router $router */
        $router = $this->app->make("router");
        $list = new RouteList();
        $list->loadRoutes($router);
    }

    private function initializeAssets()
    {
        $assets = [
            "simple_live_chat" => [
                [
                    "css",
                    "css/simple_live_chat.css",
                    [
                        "position" => Asset::ASSET_POSITION_HEADER
                    ],
                    "simple_live_chat"
                ],

                [
                    "javascript",
                    "js/simple_live_chat.js",
                    [
                        "position" => Asset::ASSET_POSITION_FOOTER
                    ],
                    "simple_live_chat"
                ]
            ]
        ];

        $assetGroups = [
            "simple_live_chat" => [
                [
                    ["javascript", "jquery"],
                    ["javascript", "underscore"],
                    ["javascript", "moment"],
                    ["javascript", "simple_live_chat"],
                    ["css", "simple_live_chat"]
                ]
            ]
        ];

        $al = AssetList::getInstance();
        $al->registerMultiple($assets);
        $al->registerGroupMultiple($assetGroups);
    }

    private function initializeAutoloader()
    {
        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        /** @var Package|PackageEntity $pkg */
        $pkg = $packageService->getByHandle("simple_live_chat");
        if ($pkg instanceof PackageEntity) {
            $autoloaderFile = $pkg->getPackagePath() . "/vendor/autoload.php";
            if (file_exists($autoloaderFile)) {
                /** @noinspection PhpIncludeInspection */
                require_once($autoloaderFile);
            }
        }
    }

    /** @noinspection DuplicatedCode */
    private function initializeChatButton()
    {
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->app->make(EventDispatcherInterface::class);
        $eventDispatcher->addListener("on_page_view", function ($pageEvent) {
            /** @var Event $pageEvent */
            $page = $pageEvent->getPageObject();

            if ($page instanceof Page && !$page->isAdminArea() && !$page->isEditMode()) {
                /** @var Config $config */
                $config = $this->app->make(Config::class);

                /*
                 * Add chat bubble markup
                 */

                $a = new Element("a");

                $a->addClass("slc-bubble");

                /** @var Identifier $idHelper */
                $idHelper = $this->app->make(Identifier::class);
                $chatPopupId = "slc-chat-popup-" . $idHelper->getString(16);

                if ($config->getChatBubbleUsePopup()) {
                    $a->setAttribute("href", "javascript:void(0);");

                    ob_start();

                    View::element("simple_live_chat", [
                        "chatHeight" => $config->getChatPopupChatHeight() + 15,
                        "textColor" => $config->getChatPopupTextColor(),
                        "buttonColor" => $config->getChatPopupButtonColor(),
                        "borderColor" => $config->getChatPopupBorderColor(),
                        "backgroundColor" => $config->getChatPopupBackgroundColor(),
                        "timeColor" => $config->getChatPopupTimeColor(),
                        "incomingMessageBubbleTextColor" => $config->getChatPopupIncomingMessageBubbleTextColor(),
                        "incomingMessageBubbleBackgroundColor" => $config->getChatPopupIncomingMessageBubbleBackgroundColor(),
                        "outgoingMessageBubbleTextColor" => $config->getChatPopupOutgoingMessageBubbleTextColor(),
                        "outgoingMessageBubbleBackgroundColor" => $config->getChatPopupOutgoingMessageBubbleBackgroundColor(),
                        "fontSize" => $config->getChatPopupFontSize(),
                        "messagePlaceholder" => $config->getChatPopupMessagePlaceholder(),
                        "buttonHelpText" => $config->getChatPopupButtonHelpText(),
                    ], "simple_live_chat");

                    $chatPopupHtml = ob_get_contents();

                    ob_end_clean();

                    $a->setAttribute("data-chat-popup-id", $chatPopupId);

                    $chatPopup = new Element("div");
                    $chatPopup->setValue($chatPopupHtml);
                    $chatPopup->setAttribute("id", $chatPopupId);
                    $chatPopup->addClass("slc-chat-popup-container");
                    $chatPopup->addClass("slc-hidden");
                    $chatPopup->addClass("slc-align-" . str_replace("_", "-", $config->getChatBubbleAlignment()));

                    View::getInstance()->addFooterItem((string)$chatPopup);

                } else {
                    $chatPage = Page::getByID($config->getChatBubbleChatPageId());

                    /** @noinspection PhpUndefinedMethodInspection */
                    if ($chatPage instanceof Page && !$chatPage->isError()) {
                        $a->setAttribute("href", (string)Url::to($chatPage));
                    } else {
                        $a->setAttribute("href", "javascript:void(0);");
                    }
                }

                $icon = new FontAwesomeIconFormatter($config->getChatBubbleIcon());

                $a->appendChild($icon->getListIconElement());

                View::getInstance()->addFooterItem((string)$a);

                /*
                 * Add stylesheet
                 */

                $alignment = explode("_", $config->getChatBubbleAlignment());

                if (isset($alignment[0]) && $alignment[1]) {
                    $css =
                        ".slc-bubble {\n" .
                        "  position: fixed;\n" .
                        "  z-index: 999999;\n" .
                        "  background-color: " . $config->getChatBubbleBackgroundColorNormal() . ";\n" .
                        "  width: " . $config->getChatBubbleSize() . "px;\n" .
                        "  height: " . $config->getChatBubbleSize() . "px;\n" .
                        "  border-radius: " . $config->getChatBubbleBorderRadius() . "px;\n" .
                        "  " . $alignment[0] . ": 15px;\n" .
                        "  " . $alignment[1] . ": 15px;\n" .
                        "}\n" .
                        "\n" .
                        ".slc-bubble i {\n" .
                        "  position: absolute;\n" .
                        "  width: " . $config->getChatBubbleSize() . "px;\n" .
                        "  height: " . $config->getChatBubbleSize() . "px;\n" .
                        "  line-height: " . $config->getChatBubbleSize() . "px;\n" .
                        "  font-size: " . ($config->getChatBubbleSize() / 2) . "px;\n" .
                        "  color: " . $config->getChatBubbleTextColorNormal() . ";\n" .
                        "  text-align: center;\n" .
                        "}\n" .
                        "\n" .
                        ".slc-bubble:hover {\n" .
                        "  background-color: " . $config->getChatBubbleBackgroundColorActive() . ";\n" .
                        "}" .
                        "\n" .
                        ".slc-bubble:hover i {\n" .
                        "  color: " . $config->getChatBubbleTextColorActive() . ";\n" .
                        "}\n";

                    if ($config->getChatBubbleUsePopup()) {
                        $spacingBetweenPopup = 5;

                        switch ($config->getChatBubbleAlignment()) {
                            case ChatBubbleAlignment::BOTTOM_RIGHT:
                                $css .=
                                    "\n" .
                                    "#" . $chatPopupId . " {\n" .
                                    "  position: fixed;\n" .
                                    "  z-index: 999;\n" .
                                    "  width: 300px;\n" .
                                    "  font-family: " . $config->getChatPopupFontFamily(). ";\n" .
                                    "  height: " . (15 + $spacingBetweenPopup + $config->getChatPopupChatHeight() + $config->getChatBubbleSize()) . "px;\n" .
                                    "  bottom: " . (15 + $spacingBetweenPopup + 5 + $config->getChatBubbleSize()) . "px;\n" .
                                    "  right: 15px;\n" .
                                    "}\n" .
                                    "\n" .
                                    "#" . $chatPopupId . "::after {\n" .
                                    "  content: \" \";;\n" .
                                    "  position: absolute;\n" .
                                    "  width: 0;\n" .
                                    "  height: 0;\n" .
                                    "  border-style: solid;\n" .
                                    "  border-top-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-bottom-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-left-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-right-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-top-color: " . $config->getChatPopupBorderColor() . ";\n" .
                                    "  border-bottom-color: transparent;\n" .
                                    "  border-right-color: transparent;\n" .
                                    "  border-left-color: transparent;\n" .
                                    "  right: " . ($config->getChatBubbleSize() / 2 - $spacingBetweenPopup * 2) . "px;\n" .
                                    "  bottom: " . (($spacingBetweenPopup * 3 + 1) * -1) . "px;\n" .
                                    "}\n";
                                break;

                            case ChatBubbleAlignment::BOTTOM_LEFT:
                                $css .=
                                    "\n" .
                                    "#" . $chatPopupId . " {\n" .
                                    "  position: fixed;\n" .
                                    "  z-index: 999;\n" .
                                    "  width: 300px;\n" .
                                    "  font-family: " . $config->getChatPopupFontFamily(). ";\n" .
                                    "  height: " . (15 + $spacingBetweenPopup + $config->getChatPopupChatHeight() + $config->getChatBubbleSize()) . "px;\n" .
                                    "  bottom: " . (15 + $spacingBetweenPopup + 5 + $config->getChatBubbleSize()) . "px;\n" .
                                    "  left: 15px;\n" .
                                    "}\n" .
                                    "\n" .
                                    "#" . $chatPopupId . "::after {\n" .
                                    "  content: \" \";;\n" .
                                    "  position: absolute;\n" .
                                    "  width: 0;\n" .
                                    "  height: 0;\n" .
                                    "  border-style: solid;\n" .
                                    "  border-top-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-bottom-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-left-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-right-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-top-color: " . $config->getChatPopupBorderColor() . ";\n" .
                                    "  border-bottom-color: transparent;\n" .
                                    "  border-right-color: transparent;\n" .
                                    "  border-left-color: transparent;\n" .
                                    "  left: " . ($config->getChatBubbleSize() / 2 - $spacingBetweenPopup * 2) . "px;\n" .
                                    "  bottom: " . (($spacingBetweenPopup * 3 + 1) * -1) . "px;\n" .
                                    "}\n";
                                break;

                            case ChatBubbleAlignment::TOP_RIGHT:
                                $css .=
                                    "\n" .
                                    "#" . $chatPopupId . " {\n" .
                                    "  position: fixed;\n" .
                                    "  z-index: 999;\n" .
                                    "  width: 300px;\n" .
                                    "  font-family: " . $config->getChatPopupFontFamily(). ";\n" .
                                    "  height: " . (15 + $spacingBetweenPopup + $config->getChatPopupChatHeight() + $config->getChatBubbleSize()) . "px;\n" .
                                    "  top: " . (15 + $spacingBetweenPopup + 10 + $config->getChatBubbleSize()) . "px;\n" .
                                    "  right: 15px;\n" .
                                    "}\n" .
                                    "\n" .
                                    "#" . $chatPopupId . "::after {\n" .
                                    "  content: \" \";;\n" .
                                    "  position: absolute;\n" .
                                    "  width: 0;\n" .
                                    "  height: 0;\n" .
                                    "  border-style: solid;\n" .
                                    "  border-top-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-bottom-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-left-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-right-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-bottom-color: " . $config->getChatPopupBorderColor() . ";\n" .
                                    "  border-top-color: transparent;\n" .
                                    "  border-right-color: transparent;\n" .
                                    "  border-left-color: transparent;\n" .
                                    "  right: " . ($config->getChatBubbleSize() / 2 - $spacingBetweenPopup * 2) . "px;\n" .
                                    "  top: " . ($spacingBetweenPopup * 4 * -1) . "px;\n" .
                                    "}\n";
                                break;

                            case ChatBubbleAlignment::TOP_LEFT:
                                $css .=
                                    "\n" .
                                    "#" . $chatPopupId . " {\n" .
                                    "  position: fixed;\n" .
                                    "  z-index: 999;\n" .
                                    "  width: 300px;\n" .
                                    "  font-family: " . $config->getChatPopupFontFamily(). ";\n" .
                                    "  height: " . (15 + $spacingBetweenPopup + $config->getChatPopupChatHeight() + $config->getChatBubbleSize()) . "px;\n" .
                                    "  top: " . (15 + $spacingBetweenPopup + 10 + $config->getChatBubbleSize()) . "px;\n" .
                                    "  left: 15px;\n" .
                                    "}\n" .
                                    "\n" .
                                    "#" . $chatPopupId . "::after {\n" .
                                    "  content: \" \";;\n" .
                                    "  position: absolute;\n" .
                                    "  width: 0;\n" .
                                    "  height: 0;\n" .
                                    "  border-style: solid;\n" .
                                    "  border-top-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-bottom-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-left-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-right-width: " . ($spacingBetweenPopup * 2) . "px;\n" .
                                    "  border-bottom-color: " . $config->getChatPopupBorderColor() . ";\n" .
                                    "  border-top-color: transparent;\n" .
                                    "  border-right-color: transparent;\n" .
                                    "  border-left-color: transparent;\n" .
                                    "  left: " . ($config->getChatBubbleSize() / 2 - $spacingBetweenPopup * 2) . "px;\n" .
                                    "  top: " . ($spacingBetweenPopup * 4 * -1) . "px;\n" .
                                    "}\n";
                                break;
                        }

                        $js =
                            '(function($) {' .
                            '  $(function(){' .
                            '    $(".slc-bubble").on("click", function() { $("#" + $(this).data("chatPopupId")).toggleClass("slc-hidden"); });' .
                            '  });' .
                            '})(jQuery);';

                        
                        $script = new Element("script");
                        $script->setValue($js);
                        View::getInstance()->addFooterItem((string)$script);
                    }

                    $style = new Element("style");
                    $style->setAttribute("style", "text/css");
                    $style->setValue($css);

                    View::getInstance()->addHeaderItem((string)$style);
                }
            }
        });
    }
}