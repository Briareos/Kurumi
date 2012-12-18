<?php

namespace Kurumi\MainBundle\OAuth\Login;

use Kurumi\MainBundle\Entity\Profile;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Kurumi\MainBundle\Entity\User;

class GoogleUserProvider extends AbstractUserProvider
{
    public function getName()
    {
        return 'google';
    }

    public function getUserEmailByResponse(UserResponseInterface $response)
    {
        return $response->getResponse()['email'];
    }

    public function getOAuthIdByResponse(UserResponseInterface $response)
    {
        return $response->getResponse()['id'];
    }

    public function fillUserInfo(User $user, UserResponseInterface $response)
    {
        $info = $response->getResponse();
        $user->setEmail($info['email']);
        if (!empty($info['given_name'])) {
            $user->setName($info['given_name']);
        } else {
            $user->setName($info['name']);
        }
    }

    public function fillProfileInfo(Profile $profile, UserResponseInterface $info)
    {
        $info = $info->getResponse();
        if (!empty($info['birthday'])) {
            $birthday = \DateTime::createFromFormat('m/d/Y', $info['birthday']);
            $profile->setBirthday($birthday);
        }
        if (!empty($info['given_name'])) {
            $profile->setFirstName($info['given_name']);
        }
        if (!empty($info['family_name'])) {
            $profile->setLastName($info['family_name']);
        }
        if (!empty($info['gender'])) {
            if ($info['gender'] === 'male') {
                $profile->setGender(Profile::GENDER_MALE);
            } else {
                $profile->setGender(Profile::GENDER_FEMALE);
            }
        }
    }

    public function getCityName(UserResponseInterface $response)
    {
        return null;
    }

    public function isVerifiedEmail(UserResponseInterface $response)
    {
        return $response->getResponse()['verified_email'];
    }
}