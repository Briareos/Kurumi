<?php

namespace Kurumi\UserBundle\City;

use Kurumi\UserBundle\Entity\CityInterface;

interface CityFinderInterface
{
    /**
     * @param $city
     * @param $name
     * @return CityInterface|null
     */
    public function find(CityInterface $city, $name);
}