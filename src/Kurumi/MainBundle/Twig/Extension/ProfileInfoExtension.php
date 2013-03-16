<?php

namespace Kurumi\MainBundle\Twig\Extension;

use Kurumi\MainBundle\Entity\Profile;
use Kurumi\MainBundle\InfoProvider\ProfileInfoProvider;

class ProfileInfoExtension extends \Twig_Extension
{
    private $profileInfoProvider;

    public function __construct(ProfileInfoProvider $profileInfoProvider)
    {
        $this->profileInfoProvider = $profileInfoProvider;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'profile_info';
    }

    public function getFunctions()
    {
        return [
            'count_pictures' => new \Twig_Function_Method($this, 'countPictures'),
            'count_profile_pictures' => new \Twig_Function_Method($this, 'countProfilePictures'),
            'count_public_pictures' => new \Twig_Function_Method($this, 'countPublicPictures'),
            'count_private_pictures' => new \Twig_Function_Method($this, 'countPrivatePictures'),
            'has_pictures' => new \Twig_Function_Method($this, 'hasPictures'),
            'has_profile_pictures' => new \Twig_Function_Method($this, 'hasProfilePictures'),
            'has_public_pictures' => new \Twig_Function_Method($this, 'hasPublicPictures'),
            'has_private_pictures' => new \Twig_Function_Method($this, 'hasPrivatePictures'),
            'profile_search_info' => new \Twig_Function_Method($this, 'profileSearchInfo'),
        ];
    }

    public function profileSearchInfo(Profile $profile)
    {
        return $this->profileInfoProvider->getSearchInfo($profile);
    }

    public function hasPictures(Profile $profile)
    {
        return $this->profileInfoProvider->hasPictures($profile);
    }

    public function hasProfilePictures(Profile $profile)
    {
        return $this->profileInfoProvider->hasProfilePictures($profile);
    }

    public function hasPublicPictures(Profile $profile)
    {
        return $this->profileInfoProvider->hasPublicPictures($profile);
    }

    public function hasPrivatePictures(Profile $profile)
    {
        return $this->profileInfoProvider->hasPrivatePictures($profile);
    }

    public function countPictures(Profile $profile)
    {
        return $this->profileInfoProvider->countPictures($profile);
    }

    public function countProfilePictures(Profile $profile)
    {
        return $this->profileInfoProvider->countProfilePictures($profile);
    }

    public function countPublicPictures(Profile $profile)
    {
        return $this->profileInfoProvider->countPublicPictures($profile);
    }

    public function countPrivatePictures(Profile $profile)
    {
        return $this->profileInfoProvider->countPrivatePictures($profile);
    }
}
