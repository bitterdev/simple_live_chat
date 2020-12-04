<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat\Controller;

use Bitter\SimpleLiveChat\Config;
use Bitter\SimpleLiveChat\Server;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;

class Api implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $config;
    protected $responseFactory;
    /** @var Package|PackageEntity */
    protected $pkg;
    protected $server;
    protected $request;

    public function __construct(
        ResponseFactory $responseFactory,
        Config $config,
        PackageService $packageService,
        Server $server,
        Request $request
    )
    {
        $this->responseFactory = $responseFactory;
        $this->config = $config;
        $this->pkg = $packageService->getByHandle("simple_live_chat");
        $this->server = $server;
        $this->request = $request;
    }

    public function hideReminder()
    {
        $this->pkg->getConfig()->save('reminder.hide', true);
        return $this->responseFactory->create("", Response::HTTP_OK);
    }

    public function hideDidYouKnow()
    {
        $this->pkg->getConfig()->save('did_you_know.hide', true);
        return $this->responseFactory->create("", Response::HTTP_OK);
    }

    public function hideLicenseCheck()
    {
        $this->pkg->getConfig()->save('license_check.hide', true);
        return $this->responseFactory->create("", Response::HTTP_OK);
    }

    public function getConfig()
    {
        return $this->responseFactory->json($this->config);
    }


    public function startServer()
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();

        if (($this->request->query->get("secret") === $this->config->getAdminSecret())) {
            $errorList = $this->server->start();
        } else {
            $errorList->add(t("You don't have the permission to control the server."));
        }

        if (!$errorList->has()) {
            $editResponse->setTitle(t("Server started"));
            $editResponse->setMessage(t("The server has been started successfully."));
        }

        $editResponse->setError($errorList);

        return $this->responseFactory->json($editResponse);
    }

    public function stopServer()
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();

        if (($this->request->query->get("secret") === $this->config->getAdminSecret())) {
            $errorList = $this->server->stop();
        } else {
            $errorList->add(t("You don't have the permission to control the server."));
        }

        if (!$errorList->has()) {
            $editResponse->setTitle(t("Server stopped"));
            $editResponse->setMessage(t("The server has been stopped successfully."));
        }

        $editResponse->setError($errorList);

        return $this->responseFactory->json($editResponse);
    }
}