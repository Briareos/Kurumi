<?php

namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Serializable;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\Facebook;
use App\UserBundle\Entity\Role;
use App\NodejsBundle\Entity\ChatUser;
use Application\Sonata\MediaBundle\Entity\Media;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;


/**
 * App\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, EquatableInterface, Serializable, ChatSubjectInterface
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
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

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
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $userRoles;


    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->password = null;
        $this->timezone = null;
        $this->active = true;
        $this->userRoles = new ArrayCollection();
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
        return $this->getUserRoles()->toArray();
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

    function __toString()
    {
        return sprintf('%s (%s)', $this->getName(), $this->getEmail());
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
     * Add userRoles
     *
     * @param Role $userRole
     * @return User
     */
    public function addUserRole(Role $role)
    {
        $this->userRoles->add($role);
        return $this;
    }

    public function removeUserRole(Role $role)
    {
        $this->userRoles->removeElement($role);
        return $this;
    }

    /**
     * Get userRoles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * Add userRoles
     *
     * @param App\UserBundle\Entity\Role $userRoles
     * @return User
     */
    public function addRole(\App\UserBundle\Entity\Role $userRoles)
    {
        $this->userRoles[] = $userRoles;
        return $this;
    }


    /**
     * Set chatCache
     *
     * @param App\NodejsBundle\Entity\ChatCache $chatCache
     * @return User
     */
    public function setChatCache(\App\NodejsBundle\Entity\ChatCache $chatCache = null)
    {
        $this->chatCache = $chatCache;
        return $this;
    }

    /**
     * Get chatCache
     *
     * @return App\NodejsBundle\Entity\ChatCache 
     */
    public function getChatCache()
    {
        return $this->chatCache;
    }

    /**
     * Set picture
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $picture
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
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getPicture()
    {
        return $this->picture;
    }
}