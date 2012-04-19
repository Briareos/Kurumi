<?php

namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Serializable;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\Facebook;

/**
 * App\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, EquatableInterface, Serializable
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
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"remove"})
     */
    private $profile;

    /**
     * @var Facebook
     *
     * @ORM\OneToOne(targetEntity="Facebook", mappedBy="user", cascade={"remove"})
     */
    private $facebook;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
        return array('ROLE_USER');
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->getId() == $user->getId();
    }

    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return Boolean true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    function isEnabled()
    {
        return $this->getActive();
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    function isAccountNonExpired()
    {
        // TODO: Implement isAccountNonExpired() method.
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return Boolean true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    function isAccountNonLocked()
    {
        // TODO: Implement isAccountNonLocked() method.
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    function isCredentialsNonExpired()
    {
        // TODO: Implement isCredentialsNonExpired() method.
        return true;
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
     * @param boolean $emptyPassword
     */
    public function setEmptyPassword($emptyPassword)
    {
        $this->emptyPassword = $emptyPassword;
    }

    /**
     * @return boolean
     */
    public function getEmptyPassword()
    {
        return $this->emptyPassword;
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
     * @param App\UserBundle\Entity\Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
        $profile->setUser($this);
    }

    /**
     * Get profile
     *
     * @return App\UserBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return \App\UserBundle\Entity\Facebook
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param \App\UserBundle\Entity\Facebook $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Set created
     *
     * @param datetime $created
     * @return User
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
}