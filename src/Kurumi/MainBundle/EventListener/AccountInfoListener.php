<?php

namespace Kurumi\MainBundle\EventListener;

use Kurumi\MainBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Kurumi\MainBundle\Controller\SearchController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AccountInfoListener implements EventSubscriberInterface
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

        if ($activeController === 'Kurumi\MainBundle\Controller\ProfileController::viewAction'
            && in_array($event->getRequest()->attributes->get('id'), [null, $user->getId()])
        ) {
            if ($this->profileIsPartial($user)) {
                $this->fillAccount($event->getRequest(), 'profile');
            }
        }

        if ($activeController === 'Kurumi\MainBundle\Controller\SearchController::searchAction') {
            if ($this->profileIsPartial($user)) {
                $this->fillAccount($event->getRequest(), 'search');
            }
        }
    }

    public function profileIsPartial(User $user)
    {
        if (null === $user->getProfile()
            || null === $user->getProfile()->getAge()
            || null === $user->getProfile()->getGender()
            || null === $user->getProfile()->getLookingFor()
            || null === $user->getProfile()->getCity()
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

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 32]],
        ];
    }
}
