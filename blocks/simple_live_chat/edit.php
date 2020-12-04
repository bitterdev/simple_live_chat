<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;
use Concrete\Core\Utility\Service\Identifier;

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
/** @var BlockView $view */
/** @var Form $form */
$app = Application::getFacadeApplication();
/** @var Color $colorPicker */
$colorPicker = $app->make(Color::class);
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'simple_live_chat');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "simple_live_chat", "rateUrl" => "https://www.concrete5.org/marketplace/addons/simple-live-chat/reviews"], 'simple_live_chat');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "entity_designer"], 'simple_live_chat');

$viewId = "dialog-" . $idHelper->getString(16);
?>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    body div.ui-dialog {
        overflow: unset;
    }
</style>

<div id="<?php echo $viewId; ?>">
    <fieldset>
        <legend>
            <?php echo t("General Style and Settings"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("textColor", t('Text Color')); ?>

            <div>
                <?php $colorPicker->output("textColor", $textColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("buttonColor", t('Button Color')); ?>

            <div>
                <?php $colorPicker->output("buttonColor", $buttonColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("borderColor", t('Border Color')); ?>

            <div>
                <?php $colorPicker->output("borderColor", $borderColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("backgroundColor", t('Background Color')); ?>

            <div>
                <?php $colorPicker->output("backgroundColor", $backgroundColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("timeColor", t('Time Color')); ?>

            <div>
                <?php $colorPicker->output("timeColor", $timeColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("fontSize", t('Base Font Size')); ?>

            <div class="input-group">
                <?php echo $form->number("fontSize", $fontSize, ["min" => 1, "max" => 99]); ?>

                <div class="input-group-addon">
                    <?php echo t("px"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("chatHeight", t('Chat Height')); ?>

            <div class="input-group">
                <?php echo $form->number("chatHeight", $chatHeight, ["min" => 1]); ?>

                <div class="input-group-addon">
                    <?php echo t("px"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("buttonHelpText", t('Button Help Text')); ?>
            <?php echo $form->text("buttonHelpText", $buttonHelpText, ["maxlength" => 255]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("messagePlaceholder", t('Message Placeholder Text')); ?>
            <?php echo $form->text("messagePlaceholder", $messagePlaceholder, ["maxlength" => 255]); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?php echo t("Incoming Message Bubble Style"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("incomingMessageBubbleTextColor", t('Text Color')); ?>

            <div>
                <?php $colorPicker->output("incomingMessageBubbleTextColor", $incomingMessageBubbleTextColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("incomingMessageBubbleBackgroundColor", t('Background Color')); ?>

            <div>
                <?php $colorPicker->output("incomingMessageBubbleBackgroundColor", $incomingMessageBubbleBackgroundColor) ?>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?php echo t("Outgoing Message Bubble Style"); ?>
        </legend>

        <div class="form-group">
            <?php echo $form->label("outgoingMessageBubbleTextColor", t('Text Color')); ?>

            <div>
                <?php $colorPicker->output("outgoingMessageBubbleTextColor", $outgoingMessageBubbleTextColor) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("outgoingMessageBubbleBackgroundColor", t('Background Color')); ?>

            <div>
                <?php $colorPicker->output("outgoingMessageBubbleBackgroundColor", $outgoingMessageBubbleBackgroundColor) ?>
            </div>
        </div>
    </fieldset>
</div>

<?php
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "simple_live_chat"], 'simple_live_chat');
?>

<script>
    /*
     * Workaround for a core bug
     */
    (function ($) {
        $(function () {
            let $dialog = $("#<?php echo $viewId; ?>");

            $dialog.closest(".ui-dialog").on("dialogopen", function () {
                let $dialogContent = $dialog.closest(".ui-dialog-content");
                if ($dialogContent.height() === 0) {
                    setTimeout(function() {
                        $dialogContent.css("height", "500px");
                    }, 200);
                }
            });
        });
    })(jQuery);
</script>