<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Kurumi\UserBundle\Entity\Profile;
use Application\Sonata\MediaBundle\Entity\Media;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;
use Briareos\AclBundle\Entity\AclSubjectInterface;

/**
 * Kurumi\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Kurumi\UserBundle\Entity\UserRepository")
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
     * @var string $locale
     *
     * @ORM\Column(name="locale", type="string", length=10, nullable=true)
     */
    private $locale;

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
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $profile;

    /**
     * @var Media
     *
     * @ORM\OneToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="picture_id", onDelete="SET NULL")
     */
    private $picture;

    /**
     * @var OAuth
     *
     * @ORM\OneToMany(targetEntity="OAuth", mappedBy="user", orphanRemoval=true)
     */
    private $oauth;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Briareos\AclBundle\Entity\AclRole", inversedBy="subjects")
     * @ORM\JoinTable(name="acl__subject_role",
     *  joinColumns={@ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="aclRole_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $aclRoles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastActiveAt", type="datetime", nullable=true)
     */
    private $lastActiveAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLoginAt", type="datetime", nullable=true)
     */
    private $lastLoginAt;

    /**
     * Used only during form validation, when the user is required to enter his current password to validate a form.
     *
     * @var null|string
     */
    private $currentPassword;


    public function __construct()
    {
        $this->salt = $this->generateSalt();
        $this->password = null;
        $this->timezone = null;
        $this->oauth = new ArrayCollection();
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

    public static function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
        if ($plainPassword !== null) {
            $this->setUpdatedAt(new \DateTime());
        }
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
        return $this;
    }

    /**
     * Set profile
     *
     * @param Profile|null $profile
     */
    public function setProfile(Profile $profile = null)
    {
        $this->profile = $profile;
        if (null !== $profile) {
            $profile->setUser($this);
        }
        return $this;
    }

    /**
     * Get profile
     *
     * @return Profile|null
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(\DateTime $createdAt)
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
        return $this;
    }

    public function isPasswordClear()
    {
        return (null === $this->password);
    }

    /**
     * Set picture
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $picture
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

    /**
     * @return string
     */
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
        $this->aclRoles[] = $aclRoles;
        return $this;
    }

    /**
     * Remove aclRoles
     *
     * @param \Briareos\AclBundle\Entity\AclRole $aclRoles
     * @return User
     */
    public function removeAclRole(\Briareos\AclBundle\Entity\AclRole $aclRoles)
    {
        $this->aclRoles->removeElement($aclRoles);
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

    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return null|string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @param null|string $currentPassword
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastActiveAt()
    {
        return $this->lastActiveAt;
    }

    /**
     * @param \DateTime $lastActiveAt
     */
    public function setLastActiveAt(\DateTime $lastActiveAt = null)
    {
        $this->lastActiveAt = $lastActiveAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @param \DateTime $lastLoginAt
     */
    public function setLastLoginAt(\DateTime $lastLoginAt = null)
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \Kurumi\UserBundle\Entity\OauthEntityInterface
     */
    public function getOauth()
    {
        return $this->oauth;
    }

    /**
     * @param \Kurumi\UserBundle\Entity\OauthEntityInterface $oauth
     */
    public function setOauth($oauth)
    {
        $this->oauth = $oauth;
    }
}
