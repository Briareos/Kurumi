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
     * @var int $gender
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
     * @ORM\ManyToOne(targetEntity="City", inversedBy="profiles", cascade={"persist"})
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $city;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     *
     */
    private $user;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @var integer $lookingFor
     *
     * @ORM\Column(name="lookingFor", type="smallint", nullable=true)
     */
    private $lookingFor;

    /**
     * @var City $lookingInCity
     *
     * @ORM\ManyToOne(targetEntity="City", cascade={"persist"})
     * @ORM\JoinColumn(name="lookingInCity_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $lookingInCity;

    /**
     * @var integer|null $lookingAgedFrom
     *
     * @ORM\Column(name="lookingAgedFrom", type="smallint", nullable=true)
     */
    private $lookingAgedFrom;

    /**
     * @var integer|null $lookingAgedTo
     *
     * @ORM\Column(name="lookingAgedTo", type="smallint", nullable=true)
     */
    private $lookingAgedTo;


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
     * @param \DoctrineExtensions\Types\Date $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get birthday
     *
     * @return \DoctrineExtensions\Types\Date
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param int $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return int
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
     * @param \DateTime $created
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
     * @return \DateTime
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
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return int
     */
    public function getLookingFor()
    {
        return $this->lookingFor;
    }

    /**
     * @param int $lookingFor
     */
    public function setLookingFor($lookingFor)
    {
        $this->lookingFor = $lookingFor;
    }

    /**
     * @return \Kurumi\UserBundle\Entity\City
     */
    public function getLookingInCity()
    {
        return $this->lookingInCity;
    }

    /**
     * @param \Kurumi\UserBundle\Entity\City $lookingInCity
     */
    public function setLookingInCity($lookingInCity)
    {
        $this->lookingInCity = $lookingInCity;
    }

    /**
     * @return int|null
     */
    public function getLookingAgedFrom()
    {
        return $this->lookingAgedFrom;
    }

    /**
     * @param int|null $lookingAgedFrom
     */
    public function setLookingAgedFrom($lookingAgedFrom)
    {
        $this->lookingAgedFrom = $lookingAgedFrom;
    }

    /**
     * @return int|null
     */
    public function getLookingAgedTo()
    {
        return $this->lookingAgedTo;
    }

    /**
     * @param int|null $lookingAgedTo
     */
    public function setLookingAgedTo($lookingAgedTo)
    {
        $this->lookingAgedTo = $lookingAgedTo;
    }
}