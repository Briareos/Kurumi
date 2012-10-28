<?php

namespace Kurumi\UserBundle\OAuth\Login;

use Kurumi\UserBundle\Entity\Profile;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Kurumi\UserBundle\Entity\User;

class FacebookUserProvider extends AbstractUserProvider
{
    public function getName()
    {
        return 'facebook';
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
        if (!empty($response['first_name'])) {
            $user->setName($response['first_name']);
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
        if (!empty($response['first_name'])) {
            $profile->setFirstName($response['first_name']);
        }
        if (!empty($response['last_name'])) {
            $profile->setLastName($response['last_name']);
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
        if (!empty($response['location']['name'])) {
            $location = $response['location']['name'];
        } elseif (!empty($response['hometown']['name'])) {
            $location = $response['hometown']['name'];
        } else {
            $location = null;
        }
        return $location;
    }

    public function isVerifiedEmail(UserResponseInterface $response)
    {
        return $response->getResponse()['verified'];
    }
}