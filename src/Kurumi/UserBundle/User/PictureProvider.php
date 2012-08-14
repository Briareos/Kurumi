<?php

namespace Kurumi\UserBundle\User;

use Doctrine\ORM\EntityManager;
use Sonata\MediaBundle\Model\Media;
use Kurumi\UserBundle\Entity\User;

class PictureProvider
{
    private $pictures = array();

    public function __construct(EntityManager $em, $className, array $pictures)
    {
        $repository = $em->getRepository($className);

        foreach (array(0 => 'none', 1 => 'male', 2 => 'female') as $genderId => $gender) {
            foreach ($pictures[$gender] as $pictureId) {
                $this->addPicture($genderId, $repository->find($pictureId));
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

    public function addPicture($genderId, Media $picture)
    {
        $this->pictures[$genderId][] = $picture;
    }
}