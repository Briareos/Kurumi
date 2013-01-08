<?php

namespace Kurumi\MainBundle\CityFinder;

use Kurumi\MainBundle\CityFinder\CityFinderInterface;
use Kurumi\MainBundle\Entity\City;

class YahooCityFinder implements CityFinderInterface
{


    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    function lookup($name)
    {
        $callback = sprintf('http://where.yahooapis.com/geocode?flags=JG&appid=%s&location=%s', urlencode($this->apiKey), urlencode($name));
        $ch = curl_init($callback);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 2,
        ));
        $response = curl_exec($ch);
        $location = json_decode($response, true);
        if (!isset($location['ResultSet']['Results'][0])) {
            return null;
        }
        $result = $location['ResultSet']['Results'][0];
        if ($result['quality'] < 39) {
            return null;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function find(City $city, $name)
    {
        $result = $this->lookup($name);
        if($result === null) {
            throw new CityNotFoundException();
        }

        $city->setLatitude($result['latitude']);
        $city->setLongitude($result['longitude']);
        $city->setCountryCode($result['level0code']);
        $city->setCountryName($result['level0']);
        if (!empty($result['level4'])) {
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