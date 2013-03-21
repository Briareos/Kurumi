<?php

namespace Kurumi\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;

class User implements UserInterface, EquatableInterface, \Serializable, ChatSubjectInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var string
     */
    private $timezone;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var ArrayCollection|OAuth[]
     */
    private $oauth;

    /**
     * @var \DateTime
     */
    private $lastActiveAt;

    /**
     * @var \DateTime
     */
    private $lastLoginAt;

    /**
     * Used only during form validation, when the user is required to enter his current password to validate a form.
     *
     * @var string
     */
    private $currentPassword;


    public function __construct()
    {
        $this->salt = $this->generateSalt();

        $this->oauth = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Generate salt.
     *
     * @return string
     */
    public static function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_IDDQD'];
    }

    public function isEqualTo(UserInterface $user)
    {
        return false;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function serialize()
    {
        return serialize($this->getId());
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        if ($plainPassword !== null) {
            $this->setUpdatedAt(new \DateTime());
        }
        $this->plainPassword = $plainPassword;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

    }

    public function setProfile(Profile $profile = null)
    {
        $this->profile = $profile;
        if (null !== $profile) {
            $profile->setUser($this);
        }

    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Clear the user's password
     *
     * @param bool $clear
     */
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

    public function getChatName()
    {
        return $this->getName();
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;
    }

    public function getLastActiveAt()
    {
        return $this->lastActiveAt;
    }

    public function setLastActiveAt(\DateTime $lastActiveAt)
    {
        $this->lastActiveAt = $lastActiveAt;
    }

    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(\DateTime $lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
