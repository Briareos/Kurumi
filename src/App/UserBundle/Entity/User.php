<?php

namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\Facebook;
use Application\Sonata\MediaBundle\Entity\Media;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;
use Briareos\AclBundle\Entity\AclSubjectInterface;

/**
 * App\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, EquatableInterface, \Serializable, ChatSubjectInterface, AclSubjectInterface
{

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var string $timezone
     *
     * @ORM\Column(name="timezone", type="string", length=255, nullable=true)
     */
    private $timezone;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", orphanRemoval=true)
     */
    private $profile;

    /**
     * @var Media
     *
     * @ORM\OneToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="picture_id", onDelete="SET NULL")
     */
    private $picture;

    /**
     * @var Facebook
     *
     * @ORM\OneToOne(targetEntity="Facebook", mappedBy="user", orphanRemoval=true)
     */
    private $facebook;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Briareos\AclBundle\Entity\AclRole", mappedBy="subjects")
     */
    private $aclRoles;


    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->password = null;
        $this->timezone = null;
        $this->aclRoles = new ArrayCollection();
    }

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
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    function getUsername()
    {
        return $this->getEmail();
    }


    public function getRoles()
    {
        return array();
    }

    public function isEqualTo(UserInterface $user)
    {
        /** @var $user User */
        return $this->getId() == $user->getId();
    }

    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    public function serialize()
    {
        return serialize($this->getId());
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
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

    /**
     * Set profile
     *
     * @param App\UserBundle\Entity\Profile|null $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        if (is_object($profile)) {
            $profile->setUser($this);
        }
    }

    /**
     * Get profile
     *
     * @return App\UserBundle\Entity\Profile|null
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return \App\UserBundle\Entity\Facebook|null
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param \App\UserBundle\Entity\Facebook|null $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setPasswordClear($clear = true)
    {
        if ($clear) {
            $this->password = null;
        }
    }

    public function isPasswordClear()
    {
        return (null === $this->password);
    }

    /**
     * Set picture
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $picture
     * @return User
     */
    public function setPicture(\Application\Sonata\MediaBundle\Entity\Media $picture = null)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * Get picture
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getPicture()
    {
        return $this->picture;
    }

    public function getChatName()
    {
        return $this->getName();
    }


    /**
     * Add aclRoles
     *
     * @param \Briareos\AclBundle\Entity\AclRole $aclRoles
     * @return User
     */
    public function addAclRole(\Briareos\AclBundle\Entity\AclRole $aclRoles)
    {
        $aclRoles->addUser($this);
        $this->aclRoles[] = $aclRoles;
        return $this;
    }

    /**
     * Get aclRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAclRoles()
    {
        return $this->aclRoles;
    }
}