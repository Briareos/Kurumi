<?php

namespace Kurumi\UserBundle\Twig\Extension;

use Twig_Extension;
use Kurumi\UserBundle\Util\GeoIp as GeoIpInstance;

class GeoIp extends Twig_Extension {

    private $geoip;

    public function __construct(GeoIpInstance $geoip) {
        $this->geoip = $geoip;
    }

    public function getGlobals() {
        return array(
          'geoip' => $this->geoip->getCurrentRecord(),
        );
    }

    public function getName()
    {
        return 'geoip';
    }

}