<?php

namespace App\NodejsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\UserBundle\Entity\User;
use App\NodejsBundle\Entity\ChatMessage;

/**
 * App\NodejsBundle\Entity\ChatUser
 *
 * @ORM\Table(name="chat__user")
 * @ORM\Entity(repositoryClass="App\NodejsBundle\Entity\ChatUserRepository")
 */
class ChatUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $sender;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $receiver;

    /**
     * @var ChatMessage
     *
     * @ORM\ManyToOne(targetEntity="ChatMessage")
     * @ORM\JoinColumn(name="clear_id", referencedColumnName="id", onDelete="NO ACTION")
     */
    private $clear;

    /**
     * @var ChatMessage
     *
     * @ORM\ManyToOne(targetEntity="ChatMessage")
     * @ORM\JoinColumn(name="last_id", referencedColumnName="id", onDelete="NO ACTION")
     */
    private $last;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sender
     *
     * @param App\UserBundle\Entity\User $sender
     * @return ChatUser
     */
    public function setSender(\App\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Get sender
     *
     * @return App\UserBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver
     *
     * @param App\UserBundle\Entity\User $receiver
     * @return ChatUser
     */
    public function setReceiver(\App\UserBundle\Entity\User $receiver = null)
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * Get receiver
     *
     * @return App\UserBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set clear
     *
     * @param App\NodejsBundle\Entity\ChatMessage $clear
     * @return ChatUser
     */
    public function setClear(\App\NodejsBundle\Entity\ChatMessage $clear = null)
    {
        $this->clear = $clear;
        return $this;
    }

    /**
     * Get clear
     *
     * @return App\NodejsBundle\Entity\ChatMessage 
     */
    public function getClear()
    {
        return $this->clear;
    }

    /**
     * Set last
     *
     * @param App\NodejsBundle\Entity\ChatMessage $last
     * @return ChatUser
     */
    public function setLast(\App\NodejsBundle\Entity\ChatMessage $last = null)
    {
        $this->last = $last;
        return $this;
    }

    /**
     * Get last
     *
     * @return App\NodejsBundle\Entity\ChatMessage 
     */
    public function getLast()
    {
        return $this->last;
    }
}