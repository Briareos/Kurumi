<?php

namespace App\UserBundle\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use App\UserBundle\Entity\User;
use Application\Sonata\MediaBundle\Entity\Media;

class UserManager
{

    private $em;

    private $encoderFactory;

    private $class;

    public function __construct(EntityManager $em, EncoderFactory $encoderFactory, $class)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->class = $class;
    }

    public function updatePassword(User $user)
    {
        if ($user->getPlainPassword()) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    public function find($id) {
        return $this->em->getRepository($this->getClass())->find($id);
    }

    public function getClass() {
        return $this->class;
    }

    public function findAll() {
        return $this->em->getRepository($this->getClass())->findAll();
    }

    public function getPicture(User $user)
    {
        $picture = $user->getPicture();
        if($picture && $picture->getEnabled()) {

        }
    }
}

