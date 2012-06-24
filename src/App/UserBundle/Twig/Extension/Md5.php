<?php

namespace App\UserBundle\Twig\Extension;

use Twig_Extension;
use App\UserBundle\Util\GeoIp as GeoIpInstance;

class Md5 extends Twig_Extension {

    public function getFilters() {
        return array(
            'md5' => new \Twig_Filter_Function('md5'),
            'addslashes' => new \Twig_Filter_Function('addslashes'),
        );
    }

    public function getName()
    {
        return 'md5';
    }

}