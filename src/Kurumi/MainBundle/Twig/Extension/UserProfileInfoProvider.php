<?php

namespace Kurumi\MainBundle\Twig\Extension;

use Kurumi\MainBundle\InfoProvider\ProfileInfoProvider;

class UserProfileInfoProvider extends \Twig_Extension
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
        return 'profile_info_provider';
    }

    public function getGlobals()
    {
        return array(
            'profile_info' => $this->profileInfoProvider,
        );
    }
}