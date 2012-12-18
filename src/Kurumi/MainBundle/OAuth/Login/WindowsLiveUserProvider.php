<?php

namespace Kurumi\MainBundle\OAuth\Login;

use Kurumi\MainBundle\Entity\Profile;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Kurumi\MainBundle\Entity\User;

class WindowsLiveUserProvider extends AbstractUserProvider
{
    public function getName()
    {
        return 'windows_live';
    }

    public function getUserEmailByResponse(UserResponseInterface $response)
    {
        return $response->getResponse()['emails']['account'];
    }

    public function getOAuthIdByResponse(UserResponseInterface $response)
    {
        return $response->getResponse()['id'];
    }

    public function fillUserInfo(User $user, UserResponseInterface $response)
    {
        $info = $response->getResponse();
        $user->setEmail($info['emails']['account']);
        if (!empty($info['first_name'])) {
            $user->setName($info['first_name']);
        } else {
            $user->setName($info['name']);
        }
    }

    public function fillProfileInfo(Profile $profile, UserResponseInterface $response)
    {
        $info = $response->getResponse();
        if (!empty($info['birthday_day']) && !empty($info['birthday_month']) && !empty($info['birthday_year'])) {
            $birthday = \DateTime::createFromFormat('m/d/Y', sprintf('%s/%s/%s', $info['birthday_month'], $info['birthday_day'], $info['birthday_year']));
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
        return null;
    }

    public function isVerifiedEmail(UserResponseInterface $response)
    {
        return true;
    }
}