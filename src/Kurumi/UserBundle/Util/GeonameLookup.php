<?php

namespace Kurumi\UserBundle\Util;

use Symfony\Component\HttpFoundation\Request;
use Zend\Cache\Manager;
use Symfony\Bridge\Monolog\Logger;

class GeonameLookup
{

    private $lookupUrl;

    private $lookupParameters;

    /**
     * @var \Zend\Cache\Frontend\Core
     */
    private $cacheFrontend;

    private $cachePrefix;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct($lookupUrl, array $lookupParameters = array())
    {
        $this->lookupUrl = $lookupUrl;
        $this->lookupParameters = $lookupParameters;
    }

    public function setCacheManager(Manager $manager, $cacheId, $cachePrefix)
    {
        $this->cacheFrontend = $manager->getCache($cacheId);
        $this->cachePrefix = $cachePrefix;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $geonameId
     * @return array|mixed|\Zend\Cache\Frontend\false
     * @throws \InvalidArgumentException
     */
    public function get($geonameId)
    {
        if (!is_numeric($geonameId) || $geonameId < 1) {
            throw new \InvalidArgumentException(sprintf('Geoname ID must be numeric.'));
        }
        if ($this->cacheFrontend) {
            $cacheId = $this->cachePrefix . '_' . intval($geonameId);
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

    /**
     * @param $geonameId
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function lookup($geonameId)
    {
        if (!is_numeric($geonameId) || $geonameId < 1) {
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
            if (12 === $data->status->value) {
                throw new \InvalidArgumentException(sprintf('Non-existent Geoname ID specified.'));
            }
            throw new \Exception(sprintf('Geoname lookup failed for ID \'%s\' on url \'%s\'. Error message: %s', $geonameId, $url, $data->status->message));
        }
        return $data;
    }
}