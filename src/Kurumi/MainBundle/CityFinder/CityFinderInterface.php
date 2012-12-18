<?php

namespace Kurumi\MainBundle\CityFinder;

use Kurumi\MainBundle\Entity\City;

interface CityFinderInterface
{
    /**
     * @param $city
     * @param $name
     * @return City|null
     */
    public function find(City $city, $name);
}