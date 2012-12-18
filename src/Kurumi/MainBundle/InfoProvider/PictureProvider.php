<?php

namespace Kurumi\MainBundle\InfoProvider;

use Doctrine\ORM\EntityManager;
use Kurumi\MainBundle\Entity\Profile;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;
use Kurumi\MainBundle\Entity\User;
use Briareos\ChatBundle\Subject\PictureProviderInterface;

class PictureProvider implements PictureProviderInterface
{
    public function __construct()
    {
    }

    public function getSubjectPicture(ChatSubjectInterface $subject)
    {
        /** @var $subject User */
        return $this->getPicture($subject);
    }

    public function getPicture(User $user)
    {
        $picture = $user->getPicture();
        if ($picture === null) {
            $uri = 'public://unknown.png';
            if ($user->getProfile() !== null) {
                if ($user->getProfile()->getGender() === Profile::GENDER_MALE) {
                    $uri = 'public://male.png';
                } elseif ($user->getProfile()->getGender() === Profile::GENDER_FEMALE) {
                    $uri = 'public://female.png';
                }
            }
        } else {
            $uri = $picture->getUri();
        }

        return $uri;
    }

}