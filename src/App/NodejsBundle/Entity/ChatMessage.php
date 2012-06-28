<?php

namespace App\NodejsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\UserBundle\Entity\User;
use App\NodejsBundle\Entity\ChatUser;

/**
 * App\NodejsBundle\Entity\ChatMessage
 *
 * @ORM\Table(name="chat__message")
 * @ORM\Entity(repositoryClass="App\NodejsBundle\Entity\ChatMessageRepository")
 */
class ChatMessage
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
     * @var text $text
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

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
     * Set text
     *
     * @param text $text
     * @return ChatMessage
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set created
     *
     * @param datetime $created
     * @return ChatMessage
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set sender
     *
     * @param App\UserBundle\Entity\User $sender
     * @return ChatMessage
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
     * @return ChatMessage
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
}