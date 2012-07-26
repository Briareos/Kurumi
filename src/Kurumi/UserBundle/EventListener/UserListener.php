<?php

namespace Kurumi\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\UserManager;

class UserListener
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->preUpdate($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            /** @var $entity User */
            $this->userManager->updatePassword($entity);
            $args->getEntityManager()->persist($entity);
            $args->getEntityManager()->flush($entity);
        }
    }
}