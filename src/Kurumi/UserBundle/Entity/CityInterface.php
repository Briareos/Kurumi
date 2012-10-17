<?php

namespace Kurumi\UserBundle\Entity;

interface CityInterface
{
    public function getName();

    public function setName($name);

    public function getState();

    public function setState($state);

    public function getCountryCode();

    public function setCountryCode($countryCode);

    public function getLatitude();

    public function setLatitude($latitude);

    public function getLongitude();

    public function setLongitude($longitude);

    public function getCountryName();

    public function setCountryName($countryName);
}