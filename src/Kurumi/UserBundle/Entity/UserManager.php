<?php

namespace Kurumi\UserBundle\Entity;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
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
}