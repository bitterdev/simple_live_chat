<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleLiveChat\Config;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\View\View;

/** @var bool $adminMode */
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
/** @var string $sidebarActiveBackgroundColor */
/** @var string $sidebarActiveTextColor */
/** @var string $sidebarNormalBackgroundColor */
/** @var string $sidebarNormalTextColor */
/** @var string $sidebarMessageCounterBackgroundColor */
/** @var string $sidebarMessageCounterTextColor */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
/** @var Config $config */
$config = $app->make(Config::class);
/** @var PackageService $packageService */
$packageService = $app->make(PackageService::class);
$hasPushNotificationSupport = $packageService->getByHandle("push_notifications") !== null;

$blockId = $idHelper->getString(16);

$view = View::getInstance();

$view->requireAsset("simple_live_chat");
$view->requireAsset("css", "font-awesome");

?>

<?php if ($adminMode && !$hasPushNotificationSupport): ?>
    <div class="alert alert-warning">
        <?php echo t(
                "If you want to have push notification support you need to purchase the %s add-on.",
            sprintf(
                "<a href=\"%s\" target=\"_blank\">%s</a>",
                "https://www.concrete5.org/marketplace/addons/push-notifications",
                t("Push Notifications")
            )
        ); ?>
    </div>
<?php endif; ?>

<script id="slc-visitor-template-<?php echo $blockId; ?>" type="text/template">
    <div class="slc-visitor" data-client-id="<%= visitor.clientId %>">
        <div class="slc-visitor-name">
            <?php echo t("Visitor"); ?>
        </div>

        <div class="slc-message-time" data-timestamp="<%= visitor.lastTimestamp %>"></div>

        <div class="slc-unread-messages-counter slc-hidden" data-unread-messages="0">

        </div>
    </div>
</script>

<script id="slc-message-template-<?php echo $blockId; ?>" type="text/template">
    <% if (message.from === clientId) { %>
    <div class="slc-message slc-outgoing-message">
        <!--suppress HtmlUnknownTarget -->
        <img src="<%= config.visitorProfilePicture %>" class="slc-profile-picture"
             alt="<?php echo t("Profile Picture"); ?>"/>

        <div class="slc-message-body">
            <%= message.body %>
        </div>

        <div class="slc-message-time" data-timestamp="<%= message.timestamp %>"></div>
    </div>
    <% } else { %>
    <div class="slc-message slc-incoming-message">
        <!--suppress HtmlUnknownTarget -->
        <img src="<%= config.agentProfilePicture %>" class="slc-profile-picture"
             alt="<?php echo t("Profile Picture"); ?>"/>

        <div class="slc-message-body">
            <%= message.body %>
        </div>

        <div class="slc-message-time" data-timestamp="<%= message.timestamp %>"></div>
    </div>
    <% } %>
</script>

<div id="slc-message-container-<?php echo $blockId; ?>"
     data-message-template-id="slc-message-template-<?php echo $blockId; ?>"
     data-visitor-template-id="slc-visitor-template-<?php echo $blockId; ?>"
     class="slc-message-container">

    <div class="slc-sidebar-container">

    </div>

    <div class="slc-chat-container">
        <div class="slc-messages-container">
            <?php echo t("Loading chat history..."); ?>
        </div>

        <div class="clear-fix"></div>

        <div class="slc-actions-container">
            <?php echo $form->text("message", null, ["class" => "slc-message-text", "placeholder" => t($messagePlaceholder)]); ?>

            <button type="button" class="slc-send-message-button" title="<?php echo h(t($buttonHelpText)); ?>">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>

        <div class="clear-fix"></div>
    </div>
</div>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    #slc-message-container-<?php echo $blockId; ?> .slc-messages-container {
        font-size: <?php echo $fontSize; ?>px;
        background-color: <?php echo $backgroundColor; ?>;
        border-color: <?php echo $borderColor; ?>;
        height: <?php echo $chatHeight; ?>px;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-visitor .slc-message-time,
    #slc-message-container-<?php echo $blockId; ?> .slc-message .slc-message-time {
        color: <?php echo $timeColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-message.slc-incoming-message .slc-message-body {
        background-color: <?php echo $incomingMessageBubbleBackgroundColor; ?>;
        color: <?php echo $incomingMessageBubbleTextColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-message.slc-outgoing-message .slc-message-body {
        background-color: <?php echo $outgoingMessageBubbleBackgroundColor; ?>;
        color: <?php echo $outgoingMessageBubbleTextColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-actions-container {
        border-color: <?php echo $borderColor; ?>;
        background-color: <?php echo $backgroundColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-actions-container .slc-message-text {
        color: <?php echo $textColor; ?>;
        background-color: <?php echo $backgroundColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-actions-container .slc-send-message-button {
        color: <?php echo $textColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?> .slc-actions-container .slc-send-message-button.disabled {
        color: <?php echo $borderColor; ?>;
    }

    .slc-message-container .slc-message .slc-profile-picture {
        border-color: <?php echo $borderColor; ?>;
    }

    <?php if($adminMode): ?>
    #slc-message-container-<?php echo $blockId; ?>.slc-admin-mode .slc-sidebar-container .slc-visitor {
        background-color: <?php echo $sidebarNormalBackgroundColor; ?>;
        color: <?php echo $sidebarNormalTextColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?>.slc-admin-mode .slc-sidebar-container .slc-visitor.active,
    #slc-message-container-<?php echo $blockId; ?>.slc-admin-mode .slc-sidebar-container .slc-visitor.active .slc-message-time {
        background-color: <?php echo $sidebarActiveBackgroundColor; ?>;
        color: <?php echo $sidebarActiveTextColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?>.slc-admin-mode .slc-sidebar-container .slc-visitor .slc-unread-messages-counter {
        background-color: <?php echo $sidebarMessageCounterBackgroundColor; ?>;
        color: <?php echo $sidebarMessageCounterTextColor; ?>;
    }

    #slc-message-container-<?php echo $blockId; ?>.slc-admin-mode .slc-sidebar-container {
        border-color: <?php echo $borderColor; ?>;
        height: <?php echo (int)$chatHeight + 40; ?>px;
    }

    <?php endif; ?>
</style>

<!--suppress JSUnresolvedVariable -->
<script>
    <?php if($adminMode): ?>
    const SLC_ADMIN_SECRET = '<?php echo h($config->getAdminSecret()); ?>';
    <?php endif; ?>

    (function ($) {
        $(function () {
            $("#slc-message-container-<?php echo $blockId; ?>").simpleLiveChat();
        });
    })(jQuery);
</script>