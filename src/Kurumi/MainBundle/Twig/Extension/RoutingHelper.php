<?php

namespace Kurumi\MainBundle\Twig\Extension;

class RoutingHelper extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'routing_helper';
    }

    public function getFunctions()
    {
        return array(
            'current_route_name' => new \Twig_Function_Method($this,'getCurrentRoute'),
        );
    }

    public function getCurrentRoute()
    {

    }
}