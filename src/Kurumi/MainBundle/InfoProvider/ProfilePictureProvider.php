<?php

namespace Kurumi\MainBundle\InfoProvider;

use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Kurumi\MainBundle\Entity\Picture;
use Kurumi\MainBundle\Entity\Profile;

class ProfilePictureProvider
{
    public function getPicture(Profile $profile)
    {
        $picture = $profile->getPicture();
        if ($picture === null) {
            $picture = new Picture();
            $uri = 'static/unknown.png';
            if ($profile->getGender() === Profile::GENDER_MALE) {
                $uri = 'static/male.png';
            } elseif ($profile->getGender() === Profile::GENDER_FEMALE) {
                $uri = 'static/female.png';
            }
            $picture->setUri($uri);
        }

        return $picture;
    }
}