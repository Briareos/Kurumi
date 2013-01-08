<?php

namespace Kurumi\MainBundle\Manager;

use Doctrine\ORM\EntityManager;
use Kurumi\MainBundle\Entity\Picture;
use Kurumi\MainBundle\Entity\Profile;
use Kurumi\MainBundle\Entity\ProfileCache;

class ProfileCacheManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCache(Profile $profile)
    {
        $profileCache = $profile->getCache();
        if ($profileCache === null) {
            $profileCache = new ProfileCache();
            $profile->setCache($profileCache);
            $profileCache->setProfile($profile);
            $this->em->persist($profileCache);
        }

        return $profileCache;
    }

    public function updatePictureCount(Profile $profile)
    {
        $cache = $this->getCache($profile);
        $count = $this->getPictureCount($profile);
        $cache->setProfilePictureCount($count);
        $this->em->persist($cache);
    }

    public function updateProfilePictureCount(Profile $profile)
    {
        $cache = $this->getCache($profile);
        $count = $this->getPictureCount($profile, Picture::PROFILE_PICTURE);
        $cache->setProfilePictureCount($count);
        $this->em->persist($cache);
    }

    public function updatePublicPictureCount(Profile $profile)
    {
        $cache = $this->getCache($profile);
        $count = $this->getPictureCount($profile, Picture::PUBLIC_PICTURE);
        $cache->setProfilePictureCount($count);
        $this->em->persist($cache);
    }

    public function updatePrivatePictureCount(Profile $profile)
    {
        $cache = $this->getCache($profile);
        $count = $this->getPictureCount($profile, Picture::PRIVATE_PICTURE);
        $cache->setProfilePictureCount($count);
        $this->em->persist($cache);
    }

    private function getPictureCount(Profile $profile, $type = null)
    {
        if ($type !== null && !in_array($type, $this->getPictureTypes())) {
            throw new \RuntimeException('Invalid picture type specified.');
        }

        $qb = $this->em->createQueryBuilder();
        $qb->from('KurumiMainBundle:Picture', 'pic');
        $qb->select('Count(pic)');
        $qb->where('pic.profile = :profile');
        $qb->setParameter('profile', $profile);
        if ($type !== null) {
            $qb->andWhere('pic.pictureType = :type');
            $qb->setParameter('type', $type);
        }
        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    private function getPictureTypes()
    {
        $types = [Picture::PROFILE_PICTURE, Picture::PUBLIC_PICTURE, Picture::PRIVATE_PICTURE];

        return array_combine($types, $types);
    }
}