<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Kurumi\UserBundle\Entity\City;
use Kurumi\UserBundle\Entity\User;
use Application\Sonata\MediaBundle\Entity\Gallery;

/**
 * Kurumi\UserBundle\Entity\Profile
 *
 * @ORM\Table(name="profile")
 * @ORM\Entity(repositoryClass="Kurumi\UserBundle\Entity\ProfileRepository")
 */
class Profile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var int
     *
     * @ORM\Column(name="gender", type="smallint", nullable=true)
     */
    private $gender;

    /**
     * @var string
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
     * @var \DateTime
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
     * @var integer
     *
     * @ORM\Column(name="lookingFor", type="smallint", nullable=true)
     */
    private $lookingFor;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="lookingAgedFrom", type="smallint", nullable=true)
     */
    private $lookingAgedFrom;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="lookingAgedTo", type="smallint", nullable=true)
     */
    private $lookingAgedTo;

    /**
     * @var Gallery
     *
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="galleryProfile_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $galleryProfile;

    /**
     * @var Gallery
     *
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="galleryPublic_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $galleryPublic;

    /**
     * @var Gallery
     *
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="galleryPrivate_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $galleryPrivate;


    public function __construct()
    {
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
     * @return \DateTime
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
     * @param City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return User
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

    public function getAge()
    {
        if (!$this->getBirthday() instanceof \DateTime) {
            return null;
        }
        return $this->getBirthday()
            ->diff(new \DateTime('now', $this->getBirthday()->getTimezone()))
            ->y;
    }

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Gallery
     */
    public function getGalleryProfile()
    {
        return $this->galleryProfile;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $galleryProfile
     */
    public function setGalleryProfile(Gallery $galleryProfile = null)
    {
        $this->galleryProfile = $galleryProfile;
    }

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Gallery
     */
    public function getGalleryPublic()
    {
        return $this->galleryPublic;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $galleryPublic
     */
    public function setGalleryPublic(Gallery $galleryPublic = null)
    {
        $this->galleryPublic = $galleryPublic;
    }

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Gallery
     */
    public function getGalleryPrivate()
    {
        return $this->galleryPrivate;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $galleryPrivate
     */
    public function setGalleryPrivate(Gallery $galleryPrivate = null)
    {
        $this->galleryPrivate = $galleryPrivate;
    }

}