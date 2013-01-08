<?php

namespace Kurumi\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class TimelineListener
{

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
    }
}