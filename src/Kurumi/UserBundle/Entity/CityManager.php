<?php

namespace Kurumi\UserBundle\Entity;

use Kurumi\UserBundle\Entity\City;
use Kurumi\UserBundle\Util\GeonameLookup;
use Doctrine\ORM\EntityManager;

class CityManager
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var GeonameLookup
     */
    private $geonameLookup;

    public function __construct($em, $geonameLookup)
    {
        $this->em = $em;
        $this->geonameLookup = $geonameLookup;
    }

    /**
     * @param City $city
     * @return City|null
     */
    public function manageCity(City $city)
    {
        if (!$city->getGeonameId()) {
            return null;
        }
        $existingCity = $this->em->getRepository(get_class($city))->findOneBy(array(
            'geonameId' => $city->getGeonameId(),
        ));
        if ($existingCity) {
            return $existingCity;
        }
        $data = $this->geonameLookup->get($city->getGeonameId());
        $city->setData($data);
        return $city;
    }
}