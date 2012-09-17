<?php

namespace Kurumi\UserBundle\User;

use Doctrine\ORM\EntityManager;
use Sonata\MediaBundle\Model\MediaInterface;
use Kurumi\UserBundle\Entity\User;

class PictureProvider
{
    private $pictures = array();

    public function __construct(EntityManager $em, $className, array $galleries)
    {
        $repository = $em->getRepository($className);

        $qb = $em->createQueryBuilder();
        $qb->from($repository->getClassName(), 'g', 'g.id');
        $qb->select('g');
        $qb->addSelect('ghm');
        $qb->addSelect('m');
        $qb->innerJoin('g.galleryHasMedias', 'ghm');
        $qb->innerJoin('ghm.media', 'm');
        $qb->where($qb->expr()->in('g.id', $galleries));
        $loadedGalleries = $qb->getQuery()->execute();

        foreach (array(0 => 'unknown', 1 => 'male', 2 => 'female') as $genderId => $gender) {
            /** @var $gallery \Sonata\MediaBundle\Model\GalleryInterface */
            $gallery = $loadedGalleries[$galleries[$gender]];
            /** @var $galleryHasMedia \Sonata\MediaBundle\Model\GalleryHasMediaInterface */
            foreach ($gallery->getGalleryHasMedias() as $galleryHasMedia) {
                $this->addPicture($genderId, $galleryHasMedia->getMedia());
            }
        }
    }

    public function getPicture(User $user)
    {
        if ($user->getPicture() !== null) {
            return $user->getPicture();
        }
        $gender = $user->getProfile()->getGender() ? $user->getProfile()->getGender() : 0;
        $availablePictures = count($this->pictures[$gender]);
        $pictureIndex = $user->getId() & $availablePictures - 1;
        return $this->pictures[$gender][$pictureIndex];
    }

    public function addPicture($genderId, MediaInterface $picture)
    {
        $this->pictures[$genderId][] = $picture;
    }
}