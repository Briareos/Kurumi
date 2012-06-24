<?php

namespace App\NodejsBundle\Twig\Extension;

use App\NodejsBundle\Util\Nodejs as NodejsService;

class Nodejs extends \Twig_Extension
{

    private $nodejs;

    public function __construct(NodejsService $nodejs)
    {
        $this->nodejs = $nodejs;
    }

    public function getGlobals()
    {
        return array(
            'nodejs' => array(
                'host' => $this->nodejs->getHost(),
                'port' => $this->nodejs->getPort(),
                'connect_timeout' => $this->nodejs->getConnectTimeout(),
                'auth_token' => $this->nodejs->getAuthToken(),
                'websocket_swf_location' => $this->nodejs->getWebsocketSwfLocation(),
            ),
        );
    }

    public function getName()
    {
        return 'nodejs';
    }
}