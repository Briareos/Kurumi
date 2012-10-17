<?php

namespace Kurumi\UserBundle\City;

use Kurumi\UserBundle\City\CityFinderInterface;
use Kurumi\UserBundle\Entity\CityInterface;
use Kurumi\UserBundle\Entity\City;

class YahooCityFinder implements CityFinderInterface
{


    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    function lookup($name)
    {
        $callback = sprintf('http://where.yahooapis.com/geocode?flags=PG&appid=%s&location=%s', urlencode($this->apiKey), urlencode($name));
        $ch = curl_init($callback);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 2,
        ));
        $location = unserialize(curl_exec($ch));
        if (!isset($location['ResultSet']['Result'][0])) {
            return null;
        }
        $result = $location['ResultSet']['Result'][0];
        if ($result['quality'] < 39) {
            return null;
        }
        return $result;
    }

    /**
     * @param City
     * @param $name
     *
     * @return \Kurumi\UserBundle\Entity\City|null
     */
    public function find(CityInterface $city, $name)
    {
        $result = $this->lookup($name);
        if($result === null) {
            return null;
        }

        $city->setLatitude($result['latitude']);
        $city->setLongitude($result['longitude']);
        $city->setCountryCode($result['level0code']);
        $city->setCountryName($result['level0']);
        if ($result['level4'] !== '') {
            $city->setName($result['level4']);
        } else {
            $city->setName($result['level3']);
        }
        if ($result['level0code'] === 'US' || $result['level0code'] === 'CA') {
            $city->setState($result['level1']);
        }
        return $city;
    }
}