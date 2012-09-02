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

        foreach (array(0 => 'unknown', 1 => 'male', 2 => 'female') as $genderId => $gender) {
            /** @var $gallery \Sonata\MediaBundle\Model\GalleryInterface */
            $gallery = $repository->find($galleries[$gender]);
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
        return $this->pictures[$gender][rand(0, count($this->pictures[$gender]) - 1)];
    }

    public function addPicture($genderId, MediaInterface $picture)
    {
        $this->pictures[$genderId][] = $picture;
    }
}