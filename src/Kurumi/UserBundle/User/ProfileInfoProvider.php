<?php

namespace Kurumi\UserBundle\User;

use Kurumi\UserBundle\Entity\Profile;

class ProfileInfoProvider
{
    public function getSearchInfo(Profile $profile)
    {
        $searchInfo = "Looking for";
        if ($profile->getLookingFor() === 1) {
            $searchInfo .= " a guy";
        } elseif ($profile->getLookingFor() === 2) {
            $searchInfo .= " a girl";
        } else {
            $searchInfo .= " someone";
        }
        if ($profile->getLookingAgedFrom() || $profile->getLookingAgedTo()) {
            $searchInfo .= " aged";
            if ($profile->getLookingAgedFrom()) {
                $searchInfo .= " from " . $profile->getLookingAgedFrom();
            }
            if ($profile->getLookingAgedTo()) {
                $searchInfo .= " to " . $profile->getLookingAgedTo();
            }
        }
        if ($profile->getCity()) {
            $city = $profile->getCity();
            $searchInfo .= ", near " . $city->getName();
        }
        $searchInfo .= ".";
        return $searchInfo;
    }

    public function hasPhotos(Profile $profile)
    {
        return false;
    }

    public function isOnline(Profile $profile)
    {
        $user = $profile->getUser();
        if ($user->getLastActiveAt() > new \DateTime('-5 minute')) {
            return true;
        }
        return false;
    }
}