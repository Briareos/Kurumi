<?php

namespace Kurumi\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            /** @var $entity User */
            $this->userManager->updatePassword($entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            /** @var $entity User */
            $this->userManager->updatePassword($entity);
            $args->getEntityManager()->getUnitOfWork()->recomputeSingleEntityChangeSet($args->getEntityManager()->getClassMetadata('UserBundle:User'), $entity);
        }
    }
}