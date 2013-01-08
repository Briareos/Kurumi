<?php

namespace Kurumi\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Kurumi\MainBundle\Entity\Picture;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PictureListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Picture) {
            return;
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Picture) {
            return;
        }
    }
}