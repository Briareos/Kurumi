<?php

namespace App\UserBundle\Util;

use App\UserBundle\Entity\City;
use App\UserBundle\Util\GeonameLookup;
use Doctrine\ORM\EntityManager;

class CityManager {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var GeonameLookup
     */
    private $geonameLookup;

    public function __construct($em, $geonameLookup) {
        $this->em = $em;
        $this->geonameLookup = $geonameLookup;
    }

    public function manageCity(City $city) {
        if(!$city->getGeonameId()) {
            return null;
        }
        $existingCity = $this->em->getRepository(get_class($city))->findOneByGeonameId($city->getGeonameId());
        if($existingCity) {
            return $existingCity;
        }
        $data = $this->geonameLookup->get($city->getGeonameId());
        $city->setData($data);
        return $city;
    }
}