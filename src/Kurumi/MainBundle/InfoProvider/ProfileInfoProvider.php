<?php

namespace Kurumi\MainBundle\InfoProvider;

use Kurumi\MainBundle\Entity\Profile;
use Kurumi\MainBundle\Manager\ProfileCacheManager;
use Doctrine\ORM\EntityManager;

class ProfileInfoProvider
{
    private $em;

    private $profileCacheManager;

    public function __construct(EntityManager $em, ProfileCacheManager $profileCacheManager)
    {
        $this->em = $em;
        $this->profileCacheManager = $profileCacheManager;
    }

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

    public function hasPictures(Profile $profile)
    {
        return ($this->hasProfilePictures($profile) || $this->hasPublicPictures($profile) || $this->hasPrivatePictures($profile));
    }

    public function hasProfilePictures(Profile $profile)
    {
        return ($this->countProfilePictures($profile) > 0);
    }

    public function hasPublicPictures(Profile $profile)
    {
        return ($this->countPublicPictures($profile) > 0);
    }

    public function hasPrivatePictures(Profile $profile)
    {
        return ($this->countPrivatePictures($profile) > 0);
    }

    public function getCache(Profile $profile) {
        return $this->profileCacheManager->getCache($profile);
    }

    public function countPictures(Profile $profile)
    {
        return $this->getCache($profile)->getPictureCount();
    }

    public function countProfilePictures(Profile $profile)
    {
        return 0;
    }

    public function countPublicPictures(Profile $profile)
    {
        return 0;
    }

    public function countPrivatePictures(Profile $profile)
    {
        return 0;
    }

    public function getProfileGallery(Profile $profile)
    {
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
