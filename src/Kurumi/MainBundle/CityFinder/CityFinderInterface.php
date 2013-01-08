<?php

namespace Kurumi\MainBundle\CityFinder;

use Kurumi\MainBundle\Entity\City;

interface CityFinderInterface
{
    /**
     * @param $city
     * @param $name
     * @return City|null
     *
     * @throws CityNotFoundException When a city cannot be found.
     */
    public function find(City $city, $name);
}