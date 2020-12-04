<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleLiveChat\Block\SimpleLiveChat;

use Concrete\Core\Block\BlockController;
use Bitter\SimpleLiveChat\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends BlockController
{
    protected $btTable = "btSimpleLiveChat";
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
    }

    public function getBlockTypeDescription()
    {
        return t('Chat window for users.');
    }

    public function getBlockTypeName()
    {
        return t('Simple Live Chat');
    }

    public function validate($args)
    {
        /** @var Validation $validationService */
        $validationService = $this->app->make(Validation::class);

        $validationService->setData($args);

        $validationService->addInteger("fontSize");
        $validationService->addInteger("chatHeight");
        $validationService->addRequiredColor("textColor");
        $validationService->addRequiredColor("buttonColor");
        $validationService->addRequiredColor("borderColor");
        $validationService->addRequiredColor("backgroundColor");
        $validationService->addRequiredColor("timeColor");
        $validationService->addRequiredColor("incomingMessageBubbleBackgroundColor");
        $validationService->addRequiredColor("incomingMessageBubbleTextColor");
        $validationService->addRequiredColor("outgoingMessageBubbleBackgroundColor");
        $validationService->addRequiredColor("outgoingMessageBubbleTextColor");
        $validationService->addRequired("buttonHelpText");
        $validationService->addRequired("messagePlaceholder");

        $validationService->test();

        return $validationService->getError();
    }

    public function add()
    {
        $this->set("chatHeight", 350);
        $this->set("fontSize", 14);
        $this->set("textColor", "#646464");
        $this->set("buttonColor", "#646464");
        $this->set("borderColor", "#ececec");
        $this->set("backgroundColor", "#ffffff");
        $this->set("timeColor", "#747474");
        $this->set("incomingMessageBubbleBackgroundColor", "#75ca2a");
        $this->set("incomingMessageBubbleTextColor", "#ffffff");
        $this->set("outgoingMessageBubbleBackgroundColor", "#ebebeb");
        $this->set("outgoingMessageBubbleTextColor", "#646464");
        $this->set("buttonHelpText", t("Click here or press enter to send the message."));
        $this->set("messagePlaceholder", t("Enter your message here..."));
    }

}
