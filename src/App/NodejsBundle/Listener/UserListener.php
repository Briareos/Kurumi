<?php

namespace App\NodejsBundle\Listener;

use App\UserBundle\Entity\User;
use App\NodejsBundle\Entity\ChatCache;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserListener
{

    /**
     * Since open chat windows are comma-separated integers (user IDs), remove them manually when users are removed from
     * the database.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $args->getEntityManager();

        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof User) {
            /** @var $entity User */

            $openChatCaches = $em->createQuery('Select cc From App\NodejsBundle\Entity\ChatCache cc Where FindInSet(:user_id, cc.open) > 0')
                ->setParameter(':user_id', $entity->getId())
                ->getResult();
            if ($openChatCaches) {
                foreach ($openChatCaches as $openChatCache) {
                    /** @var $openChatCache ChatCache */
                    $openChatCache->removeOpen($entity->getId());
                    $em->persist($openChatCache);
                }
                $em->flush();
            }
        }
    }
}
