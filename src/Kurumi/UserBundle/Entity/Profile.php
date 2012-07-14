<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kurumi\UserBundle\Entity\City;
use Kurumi\UserBundle\Entity\User;

/**
 * Kurumi\UserBundle\Entity\Profile
 *
 * @ORM\Table(name="profile")
 * @ORM\Entity(repositoryClass="Kurumi\UserBundle\Entity\ProfileRepository")
 */
class Profile
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
     * @var \DateTime $birthday
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var smallint $gender
     *
     * @ORM\Column(name="gender", type="smallint", nullable=true)
     */
    private $gender;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="City", inversedBy="profiles")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $city;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile", cascade={"remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     *
     */
    private $user;

    /**
     * @var DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

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
     * Set birthday
     *
     * @param date $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get birthday
     *
     * @return date
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param smallint $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return smallint
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        if (empty($firstName)) {
            $this->firstName = null;
        } else {
            $this->firstName = $firstName;
        }
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        if (empty($lastName)) {
            $this->lastName = null;
        } else {
            $this->lastName = $lastName;
        }
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set city
     *
     * @param Kurumi\UserBundle\Entity\City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return Kurumi\UserBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set user
     *
     * @param Kurumi\UserBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Kurumi\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set created
     *
     * @param datetime $created
     * @return Profile
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

    public function __toString()
    {
        $name = trim($this->firstName . ' ' . $this->lastName);
        if (0 === mb_strlen($name)) {
            $name = (string)$this->getId();
        }
        return $name;
    }

    /**
     * @param \Kurumi\UserBundle\Entity\DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return \Kurumi\UserBundle\Entity\DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}