<?php

namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\UserBundle\Entity\User;

/**
 * App\UserBundle\Entity\Facebook
 *
 * @ORM\Table(name="facebook")
 * @ORM\Entity
 */
class Facebook
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
     * @var bigint $facebookId
     *
     * @ORM\Column(name="facebookId", type="bigint")
     */
    private $facebookId;

    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="facebook")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

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
     * Set facebookId
     *
     * @param bigint $facebookId
     * @return Facebook
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        return $this;
    }

    /**
     * Get facebookId
     *
     * @return bigint 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set created
     *
     * @param datetime $created
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
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param App\UserBundle\Entity\User $user
     * @return Facebook
     */
    public function setUser(\App\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return App\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}