<?php

/**
 * @project:   Simple Live Chat
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleLiveChat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SimpleLiveChatClient")
 */
class Client
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="`clientId`", type="string", nullable=true) *
     */
    protected $clientId = '';

    /**
     * @var string
     * @ORM\Column(name="`pushToken`", type="string", nullable=true) *
     */
    protected $pushToken = '';

    /**
     * @var string
     * @ORM\Column(name="`chatUrl`", type="string", nullable=true) *
     */
    protected $chatUrl = '';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Client
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return Client
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }

    /**
     * @param string $pushToken
     * @return Client
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getChatUrl()
    {
        return $this->chatUrl;
    }

    /**
     * @param string $chatUrl
     * @return Client
     */
    public function setChatUrl($chatUrl)
    {
        $this->chatUrl = $chatUrl;
        return $this;
    }

}
