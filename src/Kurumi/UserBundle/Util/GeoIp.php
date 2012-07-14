<?php

namespace Kurumi\UserBundle\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class GeoIp {

    private $geoip;

    private $kernel;

    private $geoipLocation;

    public function __construct($geoipLocation, Kernel $kernel) {
        $this->geoipLocation = $geoipLocation;
        $this->kernel = $kernel;
    }

    public function getRecord($ip) {
        $this->loadGeoIp();
        return \geoip_record_by_addr($this->geoip, $ip);
    }

    public function getCurrentRecord() {
        if($this->kernel->getContainer()->has('request')) {
            $request = $this->kernel->getContainer()->get('request');
            return $this->getRecord($request->getClientIp());
        } else {
            throw new ServiceNotFoundException('Service "request" is required to get current GeoIp record.');
        }
    }

    private function loadGeoIp() {
        if(null === $this->geoip) {
            require_once $this->geoipLocation.'/geoipcity.inc';
            $this->geoip = \geoip_open($this->geoipLocation.'/GeoLiteCity.dat', GEOIP_STANDARD);
        }
    }
}