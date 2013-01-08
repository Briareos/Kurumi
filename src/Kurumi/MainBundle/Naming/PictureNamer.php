<?php

namespace Kurumi\MainBundle\Naming;

use Vich\UploaderBundle\Naming\NamerInterface;
use Kurumi\MainBundle\Entity\Picture;

class PictureNamer implements NamerInterface
{
    /**
     * {@inheritdoc}
     */
    public function name($obj, $field)
    {
        /** @var $obj \Kurumi\MainBundle\Entity\Picture */
        if ($obj->getTemporary() || $obj->getPictureType() === Picture::PRIVATE_PICTURE) {
            $name = 'private-';
        } else {
            $name = 'public-';
        }
        $name .= $this->generateRandomString(16);
        $file = $obj->getFile();
        $extension = $file->guessExtension();
        if ($extension !== null) {
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
            $name .= '.' . $extension;
        }

        return $name;
    }

    public function generateRandomString($length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $characters = str_shuffle($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}