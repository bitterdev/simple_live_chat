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
use JsonSerializable;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="SimpleLiveChatMessage")
 */
class Message implements JsonSerializable
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
     * @ORM\Column(name="`from`", type="string", nullable=true) *
     */
    protected $from = '';

    /**
     * @var string
     * @ORM\Column(name="`to`", type="string", nullable=true) *
     */
    protected $to = '';

    /**
     * @var string
     * @ORM\Column(name="`body`", type="text", nullable=true) *
     */
    protected $body = '';

    /**
     * @var DateTime
     * @ORM\Column(name="`timestamp`", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Message
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return Message
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return Message
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     * @return Message
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "from" => $this->getFrom(),
            "to" => $this->getTo(),
            "timestamp" => $this->getTimestamp() instanceof DateTime ? (int)$this->getTimestamp()->format("U") * 1000 : null,
            "body" => $this->getBody()
        ];
    }
}