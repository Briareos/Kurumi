<?php

namespace App\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\UserBundle\Entity\Profile;

/**
 * App\UserBundle\Entity\City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="App\UserBundle\Entity\CityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class City
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $state
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @var string $countryName
     *
     * @ORM\Column(name="countryName", type="string", length=255)
     */
    private $countryName;

    /**
     * @var string $countryCode
     *
     * @ORM\Column(name="countryCode", type="string", length=2)
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="continentCode", type="string", length=2)
     */
    private $continentCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="geonameId", type="integer")
     */
    private $geonameId;

    /**
     * @var float $latitude
     *
     * @ORM\Column(name="latitude", type="decimal", scale=7)
     */
    private $latitude;

    /**
     * @var float $longitude
     *
     * @ORM\Column(name="longitude", type="decimal", scale=7)
     */
    private $longitude;

    /**
     * @var Profile
     *
     * @ORM\OneToMany(targetEntity="Profile", mappedBy="city")
     */
    private $profiles;

    private $data;


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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set county
     *
     * @param string $county
     */
    public function setCounty($county)
    {
        $this->county = $county;
    }

    /**
     * Get county
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Set state
     *
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    public function getFullName()
    {
        if(!$this->geonameId) {
            return '';
        }
        if ($this->countryCode == 'US') {
            return sprintf('%s, %s, %s', $this->name, $this->state, $this->countryCode);
        }
        return sprintf('%s, %s', $this->name, $this->countryName);
    }

    public function setFullName()
    {
        // This is a form type stub, since City entities are read-only.
    }

    public function __construct()
    {
        $this->profiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function selfLookup() {
        if(!$this->data) {
            throw new \Exception('City object can\'t be persisted if no data is present.');
        }
        if(empty($this->data->geonameId)) {
            throw new \Exception('Invalid data passed to City object, parameter \'geonameId\' not found.');
        }
        $this->setGeonameId($this->data->geonameId);
        $this->setName($this->data->name);
        if($this->data->countryCode == 'US') {
            $this->setState($this->data->adminName1);
        }
        $this->setCountryName($this->data->countryName);
        $this->setCountryCode($this->data->countryCode);
        $this->setContinentCode($this->data->continentCode);

        $this->setLatitude($this->data->lat);
        $this->setLongitude($this->data->lng);
    }

    /**
     * Add profiles
     *
     * @param App\UserBundle\Entity\Profile $profiles
     */
    public function addProfile(\App\UserBundle\Entity\Profile $profiles)
    {
        $this->profiles[] = $profiles;
    }

    /**
     * Get profiles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @param int $geonameId
     */
    public function setGeonameId($geonameId)
    {
        if($this->id) {
            throw new \Exception('City objects, once created, are read-only.');
        }
        $this->geonameId = $geonameId;
    }

    /**
     * @return int
     */
    public function getGeonameId()
    {
        return $this->geonameId;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $continentCode
     */
    public function setContinentCode($continentCode)
    {
        $this->continentCode = $continentCode;
    }

    /**
     * @return string
     */
    public function getContinentCode()
    {
        return $this->continentCode;
    }

    /**
     * @param string $countryName
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Remove profiles
     *
     * @param App\UserBundle\Entity\Profile $profiles
     */
    public function removeProfile(\App\UserBundle\Entity\Profile $profiles)
    {
        $this->profiles->removeElement($profiles);
    }
}