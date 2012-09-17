<?php

namespace Kurumi\UserBundle\EventListener;

use Kurumi\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Kurumi\SearchBundle\Controller\SearchController;

class AccountInfoListener
{
    private $securityContext;

    private $router;

    public function __construct(SecurityContextInterface $securityContext, Router $router)
    {
        $this->securityContext = $securityContext;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $user = $this->getUser();
        if (null === $user) {
            return;
        }

        $activeController = $event->getRequest()->attributes->get('_controller');

        if ($activeController === 'Kurumi\UserBundle\Controller\ProfileController::viewAction'
            && in_array($event->getRequest()->attributes->get('id'), array(null, $user->getId()))
        ) {
            if ($this->profileIsPartial($user)) {
                $this->fillAccount($event->getRequest(), 'profile');
            }
        }

        if ($activeController === 'Kurumi\SearchBundle\Controller\SearchController::searchAction') {
            if ($this->profileIsPartial($user)) {
                $this->fillAccount($event->getRequest(), 'search');
            }
        }
    }

    protected function profileIsPartial(User $user)
    {
        if (null === $user->getProfile()
            || null === $user->getProfile()->getAge()
            || null === $user->getProfile()->getGender()
            || null === $user->getProfile()->getCity()
            || null === $user->getProfile()->getLookingFor()
        ) {
            return true;
        }
        return false;
    }

    protected function fillAccount(Request $request, $routeName)
    {
        $request->attributes->set('route', $routeName);
        $controller = $this->router->getRouteCollection()->get('account_fill')->getDefault('_controller');
        $request->attributes->set('_route', $routeName);
        $request->attributes->set('_controller', $controller);
    }

    /**
     * @return bool|User
     */
    public function getUser()
    {
        if ($this->securityContext->getToken() === null) {
            return null;
        }
        $user = $this->securityContext->getToken()->getUser();
        if (!$user instanceof User) {
            return null;
        }
        return $user;
    }
}