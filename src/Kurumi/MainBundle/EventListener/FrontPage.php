<?php

namespace Kurumi\MainBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\User\UserInterface;

class FrontPage
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var SecurityContext
     */
    protected $security;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(Request $request, Router $router, SecurityContext $security, EventDispatcher $dispatcher)
    {
        $this->request = $request;
        $this->router = $router;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $currentRouteName = $this->request->get('_route');
        if ($currentRouteName == 'front') {
            if ($this->security->getToken()->getUser() instanceof UserInterface) {
                $newRouteName = 'profile';
            } else {
                $newRouteName = 'front_page';
            }
            $controller = $this->router->getRouteCollection()->get($newRouteName)->getDefault('_controller');
            $this->request->attributes->set('_controller', $controller);
        } elseif ($currentRouteName == 'home_page' || $currentRouteName == 'front_page') {
            $newRouteUrl = $this->router->generate('front');
            $response = new RedirectResponse($newRouteUrl);
            $event->setResponse($response);
        }
    }
}