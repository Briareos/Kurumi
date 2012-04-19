<?php

namespace App\UserBundle\Util;

use Symfony\Component\HttpFoundation\Request;

class GeonameLookup
{

    private $lookupUrl;

    private $lookupParameters;

    /**
     * @var \Zend\Cache\Frontend\Core
     */
    private $cacheFrontend;

    private $cacheId;

    public function __construct($lookupUrl, array $lookupParameters = array(), $cacheManager = null, $cacheFrontend = null, $cacheId = 'geoname_lookup')
    {
        $this->lookupUrl = $lookupUrl;
        $this->lookupParameters = $lookupParameters;
        $this->cacheId = $cacheId;
        if ($cacheManager instanceof \Zend\Cache\Manager and $cacheFrontend) {
            $this->cacheFrontend = $cacheManager->getCache($cacheFrontend);
        }
    }

    public function get($geonameId)
    {
        if(!is_numeric($geonameId)) {
            throw new \InvalidArgumentException(sprintf('Geoname ID must be numeric.'));
        }
        if ($this->cacheFrontend) {
            $cacheId = $this->cacheId . '_' . intval($geonameId);
            if ($this->cacheFrontend->test($cacheId)) {
                return $this->cacheFrontend->load($cacheId);
            } else {
                $data = $this->lookup($geonameId);
                $this->cacheFrontend->save($data, $cacheId);
                return $data;
            }
        }
        return $this->lookup($geonameId);
    }

    public function lookup($geonameId)
    {
        if(!is_numeric($geonameId)) {
            throw new \InvalidArgumentException(sprintf('Geoname ID must be numeric.'));
        }
        $parameters = $this->lookupParameters + array('geonameId' => $geonameId);
        $url = $this->lookupUrl . '?' . http_build_query($parameters);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        $data = @json_decode($response);
        if (empty($data->geonameId)) {
            if (empty($data->message)) {
                $message = '';
            } else {
                $message = $data->message;
            }
            throw new \Exception(sprintf('Geoname lookup failed for ID \'%s\' on url \'%s\'. Error message: %s', $geonameId, $url, $message));
        }
        return $data;
    }
}