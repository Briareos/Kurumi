<?php

namespace Kurumi\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class OAuth
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $oauthId;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $email;

    public function getId()
    {
        return $this->id;
    }

    public function setOauthId($oauthId)
    {
        $this->oauthId = $oauthId;
    }

    public function getOauthId()
    {
        return $this->oauthId;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}