<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var bool $isRunning */
/** @var int $port */
/** @var string $welcomeMessage */
/** @var string $offlineMessage */
/** @var string $notificationMailAddress */
/** @var bool $chatBubbleVisibility */
/** @var array $chatBubbleAlignments */
/** @var string $chatBubbleAlignment */
/** @var array $chatBubbleIcons */
/** @var string $chatBubbleIcon */
/** @var int $chatBubbleChatPageId */
/** @var int $chatBubbleSize */
/** @var int $chatBubbleBorderRadius */
/** @var string $chatBubbleBackgroundColorNormal */
/** @var string $chatBubbleTextColorNormal */
/** @var string $chatBubbleBackgroundColorActive */
/** @var string $chatBubbleTextColorActive */
/** @var int $incomingMessageSoundFileId */
/** @var int $profilePictureFileId */
/** @var int $chatPopupFontSize */
/** @var int $chatPopupChatHeight */
/** @var string $chatPopupTextColor */
/** @var string $chatPopupButtonColor */
/** @var string $chatPopupBorderColor */
/** @var string $chatPopupBackgroundColor */
/** @var string $chatPopupTimeColor */
/** @var string $chatPopupIncomingMessageBubbleBackgroundColor */
/** @var string $chatPopupIncomingMessageBubbleTextColor */
/** @var string $chatPopupOutgoingMessageBubbleBackgroundColor */
/** @var string $chatPopupOutgoingMessageBubbleTextColor */
/** @var string $chatPopupButtonHelpText */
/** @var string $chatPopupMessagePlaceholder */
/** @var bool $chatBubbleUsePopup */
/** @var string $chatPopupFontFamily */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Color $colorPicker */
$colorPicker = $app->make(Color::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);
/** @var Token $token */
$token = $app->make(Token::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'simple_live_chat');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "simple_live_chat", "rateUrl" => "https://www.concrete5.org/marketplace/addons/simple-live-chat/reviews"], 'simple_live_chat');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "entity_designer"], 'simple_live_chat');
?>

<form action="#" method="post">
    <?php echo $token->output("save_settings"); ?>

    <fieldset>
        <legend>
            <?php echo t("General"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("port", t('Port')); ?>

            <?php if ($isRunning): ?>
                <?php echo $form->number("port", $port, ["min" => 1, "max" => 65535, "disabled" => "disabled"]) ?>
            <?php else: ?>
                <?php echo $form->number("port", $port, ["min" => 1, "max" => 65535]) ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("welcomeMessage", t('Welcome Message')); ?>
            <?php echo $form->textarea("welcomeMessage", $welcomeMessage) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("offlineMessage", t('Offline Message')); ?>
            <?php echo $form->textarea("offlineMessage", $offlineMessage) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("notificationMailAddress", t('Notification Mail Address')); ?>
            <?php echo $form->email("notificationMailAddress", $notificationMailAddress) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("profilePictureFileId", t('Profile Picture')); ?>
            <?php echo $fileManager->image("profilePictureFileId", "profilePictureFileId", t("Please select file..."), File::getByID($profilePictureFileId)) ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?php echo t("Chat Bubble"); ?>
        </legend>

        <div class="checkbox">
            <label>
                <?php echo $form->checkbox("chatBubbleVisibility", null, $chatBubbleVisibility) ?>
                <?php echo t("Display chat bubble"); ?>
            </label>
        </div>

        <div id="chatBubbleOptions">
            <div class="form-group">
                <?php echo $form->label("chatBubbleAlignment", t('Alignment')); ?>
                <?php echo $form->select("chatBubbleAlignment", $chatBubbleAlignments, $chatBubbleAlignment) ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleIcon", t('Icon')); ?>
                <?php echo $form->select("chatBubbleIcon", $chatBubbleIcons, $chatBubbleIcon) ?>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleBorderRadius", t('Border Radius')); ?>

                <div class="input-group">
                    <?php echo $form->number("chatBubbleBorderRadius", $chatBubbleBorderRadius, ["min" => 0]) ?>

                    <div class="input-group-addon">
                        <?php echo t("px"); ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleSize", t('Size')); ?>

                <div class="input-group">
                    <?php echo $form->number("chatBubbleSize", $chatBubbleSize, ["min" => 0]) ?>

                    <div class="input-group-addon">
                        <?php echo t("px"); ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleBackgroundColorNormal", t('Background Color')); ?>

                <div>
                    <?php $colorPicker->output("chatBubbleBackgroundColorNormal", $chatBubbleBackgroundColorNormal) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleTextColorNormal", t('Text Color')); ?>

                <div>
                    <?php $colorPicker->output("chatBubbleTextColorNormal", $chatBubbleTextColorNormal) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleBackgroundColorActive", t('Background Color (Active State)')); ?>

                <div>
                    <?php $colorPicker->output("chatBubbleBackgroundColorActive", $chatBubbleBackgroundColorActive) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("chatBubbleTextColorActive", t('Text Color (Active State)')); ?>

                <div>
                    <?php $colorPicker->output("chatBubbleTextColorActive", $chatBubbleTextColorActive) ?>
                </div>
            </div>

            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox("chatBubbleUsePopup", null, $chatBubbleUsePopup) ?>
                    <?php echo t("Use chat popup"); ?>
                </label>
            </div>

            <div id="chatDetailPage">
                <div class="form-group">
                    <?php echo $form->label("chatBubbleChatPageId", t('Chat Page')); ?>
                    <?php echo $pageSelector->selectPage("chatBubbleChatPageId", $chatBubbleChatPageId) ?>
                </div>
            </div>

            <div id="chatPopupOptions">
                <fieldset>
                    <legend>
                        <?php echo t("Chat Popup Style and Settings"); ?>
                    </legend>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupTextColor", t('Text Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupTextColor", $chatPopupTextColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupButtonColor", t('Button Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupButtonColor", $chatPopupButtonColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupBorderColor", t('Border Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupBorderColor", $chatPopupBorderColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupBackgroundColor", t('Background Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupBackgroundColor", $chatPopupBackgroundColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupTimeColor", t('Time Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupTimeColor", $chatPopupTimeColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupFontSize", t('Base Font Size')); ?>

                        <div class="input-group">
                            <?php echo $form->number("chatPopupFontSize", $chatPopupFontSize, ["min" => 1, "max" => 99]); ?>

                            <div class="input-group-addon">
                                <?php echo t("px"); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupFontFamily", t('Font Family')); ?>
                        <?php echo $form->text("chatPopupFontFamily", $chatPopupFontFamily); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupChatHeight", t('Chat Height')); ?>

                        <div class="input-group">
                            <?php echo $form->number("chatPopupChatHeight", $chatPopupChatHeight, ["min" => 1]); ?>

                            <div class="input-group-addon">
                                <?php echo t("px"); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupButtonHelpText", t('Button Help Text')); ?>
                        <?php echo $form->text("chatPopupButtonHelpText", $chatPopupButtonHelpText, ["maxlength" => 255]); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupMessagePlaceholder", t('Message Placeholder Text')); ?>
                        <?php echo $form->text("chatPopupMessagePlaceholder", $chatPopupMessagePlaceholder, ["maxlength" => 255]); ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>
                        <?php echo t("Incoming Message Bubble Style"); ?>
                    </legend>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupIncomingMessageBubbleTextColor", t('Text Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupIncomingMessageBubbleTextColor", $chatPopupIncomingMessageBubbleTextColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupIncomingMessageBubbleBackgroundColor", t('Background Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupIncomingMessageBubbleBackgroundColor", $chatPopupIncomingMessageBubbleBackgroundColor) ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>
                        <?php echo t("Outgoing Message Bubble Style"); ?>
                    </legend>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupOutgoingMessageBubbleTextColor", t('Text Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupOutgoingMessageBubbleTextColor", $chatPopupOutgoingMessageBubbleTextColor) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->label("chatPopupOutgoingMessageBubbleBackgroundColor", t('Background Color')); ?>

                        <div>
                            <?php $colorPicker->output("chatPopupOutgoingMessageBubbleBackgroundColor", $chatPopupOutgoingMessageBubbleBackgroundColor) ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> <?php echo t("Save"); ?>
                    </button>
                </div>
            </div>
        </div>
    </fieldset>
</form>

<!--suppress JSUnresolvedVariable, JSDeprecatedSymbols -->
<script>
    (function ($) {
        $(function () {
            $("#chatBubbleVisibility").bind("change click", function () {
                if ($(this).is(":checked")) {
                    $("#chatBubbleOptions").removeClass("hidden");
                } else {
                    $("#chatBubbleOptions").addClass("hidden");
                }
            }).trigger("change");

            $("#chatBubbleUsePopup").bind("change click", function () {
                if ($(this).is(":checked")) {
                    $("#chatDetailPage").addClass("hidden");
                    $("#chatPopupOptions").removeClass("hidden");
                } else {
                    $("#chatDetailPage").removeClass("hidden");
                    $("#chatPopupOptions").addClass("hidden");
                }
            }).trigger("change");
        });
    })(jQuery);
</script>

<?php
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "simple_live_chat"], 'simple_live_chat');
?>