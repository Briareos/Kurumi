<?php

namespace Kurumi\UserBundle\OAuth\Login;

use Kurumi\UserBundle\Entity\Profile;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Kurumi\UserBundle\Entity\User;

class WindowsLiveUserProvider extends AbstractUserProvider
{
    public function getName()
    {
        return 'windows_live';
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
        $user->setEmail($response['email']);
        if (!empty($response['given_name'])) {
            $user->setName($response['given_name']);
        } else {
            $user->setName($response['name']);
        }
    }

    public function fillProfileInfo(Profile $profile, UserResponseInterface $response)
    {
        if (!empty($response['birthday'])) {
            $birthday = \DateTime::createFromFormat('m/d/Y', $response['birthday']);
            $profile->setBirthday($birthday);
        }
        if (!empty($response['given_name'])) {
            $profile->setFirstName($response['given_name']);
        }
        if (!empty($response['family_name'])) {
            $profile->setLastName($response['family_name']);
        }
        if (!empty($response['gender'])) {
            if ($response['gender'] === 'male') {
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