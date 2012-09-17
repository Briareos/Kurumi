<?php

namespace Kurumi\UserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Kurumi\UserBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserActivityListener
{
    private $em;

    private $securityContext;

    public function __construct(EntityManager $em, SecurityContextInterface $securityContext)
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return null;
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $user = $this->getUser();
        if ($user === null) {
            return;
        }
        if ($event->getResponse()->getStatusCode() !== 200) {
            return;
        }
        if ($user->getLastActiveAt() && ((new \DateTime())->getTimestamp() - $user->getLastActiveAt()->getTimestamp()) < 60) {
            return;
        }

        $user->setLastActiveAt(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $this->getUser();
        if ($user === null) {
            return;
        }
        $user->setLastLoginAt(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
    }
}