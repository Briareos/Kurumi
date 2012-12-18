<?php

namespace Kurumi\MainBundle\OAuth\Login;

use Kurumi\MainBundle\Entity\Profile;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Kurumi\MainBundle\Entity\User;

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

    public function fillProfileInfo(Profile $profile, UserResponseInterface $info)
    {
        if (!empty($info['birthday'])) {
            $birthday = \DateTime::createFromFormat('m/d/Y', $info['birthday']);
            $profile->setBirthday($birthday);
        }
        if (!empty($info['first_name'])) {
            $profile->setFirstName($info['first_name']);
        }
        if (!empty($info['last_name'])) {
            $profile->setLastName($info['last_name']);
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