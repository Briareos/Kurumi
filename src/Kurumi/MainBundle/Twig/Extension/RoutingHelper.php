<?php

namespace Kurumi\MainBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RoutingHelper extends \Twig_Extension
{
    private $request;

    function __construct(ContainerInterface $container)
    {
        $this->request = $container->get('request');
    }


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
            'current_route' => new \Twig_Function_Method($this, 'getCurrentRoute'),
            'current_route_params' => new \Twig_Function_Method($this, 'getCurrentRouteParams'),
        );
    }

    public function getCurrentRoute()
    {
        $route = $this->request->attributes->get('_route');

        return $route;
    }

    public function getCurrentRouteParams()
    {
        $params = $this->request->attributes->get('_route_params');

        return $params;
    }
}