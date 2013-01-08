<?php

namespace Kurumi\MainBundle\Twig\Extension;

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

    public function getGlobals()
    {
        return array(
            'profile_info' => $this->profileInfoProvider,
        );
    }
}