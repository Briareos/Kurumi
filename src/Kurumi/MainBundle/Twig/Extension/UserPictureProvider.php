<?php

namespace Kurumi\MainBundle\Twig\Extension;

use Kurumi\MainBundle\InfoProvider\PictureProvider;
use Kurumi\MainBundle\Entity\User;

class UserPictureProvider extends \Twig_Extension
{
    private $pictureProvider;

    public function __construct(PictureProvider $pictureProvider)
    {
        $this->pictureProvider = $pictureProvider;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'user_picture_provider';
    }

    public function getFunctions()
    {
        return array(
            'user_picture' => new \Twig_Function_Method($this, 'getPicture'),
        );
    }

    public function getPicture(User $user)
    {
        return $this->pictureProvider->getPicture($user);
    }
}