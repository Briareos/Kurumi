<?php

namespace Kurumi\MainBundle\InfoProvider;

use Kurumi\MainBundle\Entity\Profile;
use Doctrine\ORM\EntityManager;

class ProfileInfoProvider
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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

    public function hasPhotos(Profile $profile)
    {
        return ($this->hasProfilePhotos($profile) || $this->hasPublicPhotos($profile) || $this->hasPrivatePhotos($profile));
    }

    public function hasProfilePhotos(Profile $profile)
    {
        return ($this->countProfilePhotos($profile) > 0);
    }

    public function hasPublicPhotos(Profile $profile)
    {
        return ($this->countPublicPhotos($profile) > 0);
    }

    public function hasPrivatePhotos(Profile $profile)
    {
        return ($this->countPrivatePhotos($profile) > 0);
    }

    public function countPhotos(Profile $profile)
    {
        return 0;
    }

    public function countProfilePhotos(Profile $profile)
    {
        return 0;
    }

    public function countPublicPhotos(Profile $profile)
    {
        return 0;
    }

    public function countPrivatePhotos(Profile $profile)
    {
        return 0;
    }

    public function getProfileGallery(Profile $profile)
    {
        if ($profile->getGalleryProfile() === null) {
            return null;
        }
        $gallery = $this->em->createQuery('Select g From SonataMediaBundle:Gallery g Inner Join g.galleryHasMedias gm Inner Join gm.media m Where g.id = :id')->setParameter('id', $profile->getGalleryProfile()->getId())->getSingleResult();
        return $gallery;
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