<?php

namespace App\UserBundle\Util;

use Symfony\Component\HttpFoundation\Request;

class GeonameLookup {

    private $lookupUrl;

    private $lookupParameters;

    /**
     * @var \Zend\Cache\Frontend\Core
     */
    private $cacheFrontend;

    private $cacheId;

    public function __construct($lookupUrl, array $lookupParameters = array(), $cacheManager = null, $cacheFrontend = null, $cacheId = 'geoname_lookup') {
        $this->lookupUrl = $lookupUrl;
        $this->lookupParameters = $lookupParameters;
        $this->cacheId = $cacheId;
        if($cacheManager instanceof \Zend\Cache\Manager and $cacheFrontend) {
            $this->cacheFrontend = $cacheManager->getCache($cacheFrontend);
        }
    }

    public function get($geonameId) {
        if($this->cacheFrontend) {
            $cacheId = $this->cacheId.'_'.intval($geonameId);
            if($this->cacheFrontend->test($cacheId)) {
                return $this->cacheFrontend->load($cacheId);
            } else {
                $data = $this->lookup($geonameId);
                $this->cacheFrontend->save($data, $cacheId);
                return $data;
            }
        }
        return $this->lookup($geonameId);
    }

    public function lookup($geonameId) {
        $parameters = $this->lookupParameters + array('geonameId' => $geonameId);

        $ch = curl_init($this->lookupUrl . '?' . http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        $data = json_decode($response);
        return $data;
    }
}