<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kurumi\UserBundle\Entity\User;

/**
 * Kurumi\UserBundle\Entity\Facebook
 *
 * @ORM\Table(name="oauth")
 * @ORM\Entity
 */
class OAuth
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $oauthId
     *
     * @ORM\Column(name="oauthId", type="string", length=255)
     */
    private $oauthId;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="oauth")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

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
     * Set Facebook ID
     *
     * @param string $oauthId
     * @return Facebook
     */
    public function setOauthId($oauthId)
    {
        $this->oauthId = $oauthId;
        return $this;
    }

    /**
     * Get Facebook ID
     *
     * @return string
     */
    public function getOauthId()
    {
        return $this->oauthId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Facebook
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
     * Set user
     *
     * @param \Kurumi\UserBundle\Entity\User $user
     * @return Facebook
     */
    public function setUser(\Kurumi\UserBundle\Entity\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \Kurumi\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}