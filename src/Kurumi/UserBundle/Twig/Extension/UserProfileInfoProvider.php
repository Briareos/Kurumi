<?php

namespace Kurumi\UserBundle\Twig\Extension;

use Kurumi\UserBundle\User\ProfileInfoProvider;
use Kurumi\UserBundle\Entity\User;

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
        return 'user_search_info_provider';
    }

    public function getGlobals()
    {
        return array(
            'profile_info' => new $this->profileInfoProvider,
        );
    }
}