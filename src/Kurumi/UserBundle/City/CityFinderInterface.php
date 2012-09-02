<?php

namespace Kurumi\UserBundle\City;

interface CityFinderInterface
{
    public function lookup($name);

    public function find($cityClass, $name);
}