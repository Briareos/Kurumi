<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Kurumi\UserBundle\Entity\User;
use Application\Sonata\MediaBundle\Entity\Media;

class UserManager
{

    private $em;

    private $repository;

    private $encoderFactory;

    public function __construct(EntityManager $em, EncoderFactory $encoderFactory, $class)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->repository = $this->em->getRepository($class);
    }

    public function updatePassword(User $user)
    {
        if ($user->getPlainPassword()) {
            /** @var $encoder \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface */
            $encoder = $this->encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    public function getPicture(User $user)
    {
        $picture = $user->getPicture();
        if ($picture && $picture->getEnabled()) {

        }
    }
}

