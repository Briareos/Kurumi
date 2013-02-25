<?php

namespace Kurumi\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class Profile
{
    const GENDER_MALE = 1;

    const GENDER_FEMALE = 2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var int
     */
    private $gender;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var City
     */
    private $city;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var integer
     */
    private $lookingFor;

    /**
     * @var integer
     */
    private $lookingAgedFrom;

    /**
     * @var integer
     */
    private $lookingAgedTo;

    /**
     * @var ProfileCache
     */
    private $cache;

    /**
     * @var Picture
     */
    private $picture;

    /**
     * @var Picture[]
     */
    private $pictures;

    /**
     * @var PictureComment[]
     */
    private $pictureComments;


    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBirthday(\DateTime $birthday = null)
    {
        $this->birthday = $birthday;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setFirstName($firstName)
    {
        if (empty($firstName)) {
            $this->firstName = null;
        } else {
            $this->firstName = $firstName;
        }
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        if (empty($lastName)) {
            $this->lastName = null;
        } else {
            $this->lastName = $lastName;
        }
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setCity(City $city = null)
    {
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function __toString()
    {
        $name = trim($this->firstName . ' ' . $this->lastName);
        if (0 === mb_strlen($name)) {
            $name = (string) $this->getId();
        }

        return $name;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getLookingFor()
    {
        return $this->lookingFor;
    }

    public function setLookingFor($lookingFor)
    {
        $this->lookingFor = $lookingFor;
    }

    public function getLookingAgedFrom()
    {
        return $this->lookingAgedFrom;
    }

    public function setLookingAgedFrom($lookingAgedFrom)
    {
        $this->lookingAgedFrom = $lookingAgedFrom;
    }

    public function getLookingAgedTo()
    {
        return $this->lookingAgedTo;
    }

    public function setLookingAgedTo($lookingAgedTo)
    {
        $this->lookingAgedTo = $lookingAgedTo;
    }

    public function getAge()
    {
        if (!$this->getBirthday() instanceof \DateTime) {
            return null;
        }
        $age = $this->getBirthday()
          ->diff(new \DateTime('now', $this->getBirthday()->getTimezone()))
          ->y;

        return $age;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setCache(ProfileCache $cache)
    {
        $this->cache = $cache;
    }

    public function getPictures()
    {
        return $this->pictures;
    }

    public function setPictures($pictures)
    {
        $this->pictures = $pictures;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture(Picture $picture)
    {
        $this->picture = $picture;
    }

    public function removePicture()
    {
        $this->picture = null;
    }

    public function isUser($user)
    {
        return ($user instanceof User) && ($user->getId() === $this->user->getId());
    }

    public function getPictureComments()
    {
        return $this->pictureComments;
    }

    public function setPictureComments($pictureComments)
    {
        $this->pictureComments = $pictureComments;
    }
}
