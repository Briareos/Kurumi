<?php

namespace Kurumi\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProfileCache
 */
class ProfileCache
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $profilePictureCount;

    /**
     * @var integer
     */
    private $publicPictureCount;

    /**
     * @var integer
     */
    private $privatePictureCount;

    /**
     * @var integer
     */
    private $pictureCount;

    /**
     * @var Profile
     */
    private $profile;


    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setProfilePictureCount($profilePictureCount)
    {
        $this->profilePictureCount = $profilePictureCount;

        return $this;
    }

    public function getProfilePictureCount()
    {
        return $this->profilePictureCount;
    }

    public function setPublicPictureCount($publicPictureCount)
    {
        $this->publicPictureCount = $publicPictureCount;

        return $this;
    }

    public function getPublicPictureCount()
    {
        return $this->publicPictureCount;
    }

    public function setPrivatePictureCount($privatePictureCount)
    {
        $this->privatePictureCount = $privatePictureCount;

        return $this;
    }

    public function getPrivatePictureCount()
    {
        return $this->privatePictureCount;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function getPictureCount()
    {
        return $this->pictureCount;
    }

    public function setPictureCount($pictureCount)
    {
        $this->pictureCount = $pictureCount;
    }
}
