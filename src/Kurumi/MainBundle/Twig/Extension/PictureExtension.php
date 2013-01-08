<?php

namespace Kurumi\MainBundle\Twig\Extension;

use Kurumi\MainBundle\InfoProvider\ProfilePictureProvider;
use Kurumi\MainBundle\InfoProvider\PictureInfoProvider;
use Kurumi\MainBundle\Entity\Picture;
use Kurumi\MainBundle\Entity\Profile;

class PictureExtension extends \Twig_Extension
{
    private $profilePictureProvider;

    private $pictureInfoProvider;

    public function __construct(ProfilePictureProvider $profilePictureProvider, PictureInfoProvider $pictureInfoProvider)
    {
        $this->profilePictureProvider = $profilePictureProvider;
        $this->pictureInfoProvider = $pictureInfoProvider;
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
            'profile_picture' => new \Twig_Function_Method($this, 'getProfilePicture', ['is_safe' => ['html']]),
            'picture' => new \Twig_Function_Method($this, 'getPicture', ['is_safe' => ['html']]),
        );
    }

    public function getProfilePicture(Profile $profile, $format = null, $attributes = [])
    {
        $picture = $this->profilePictureProvider->getPicture($profile);

        return $this->getPictureHtml($picture, $format, $attributes);
    }

    public function getPicture(Picture $picture, $format = null, $attributes = [])
    {
        return $this->getPictureHtml($picture, $format, $attributes);
    }

    public function getPictureHtml(Picture $picture, $format = null, $attributes = [])
    {
        $url = $this->pictureInfoProvider->getPictureUrl($picture, $format);
        $pictureInfo = $this->pictureInfoProvider->getPictureInfo($picture, $format);
        if (isset($pictureInfo[0]) && isset($pictureInfo[1])) {
            $attributes += [
                'width' => $pictureInfo[0],
                'height' => $pictureInfo[1],
            ];
        }
        $attributes += ['alt' => ''];
        $attributes = $this->convertAttributesToString($attributes);
        $html = <<<HTML
<img src="{$url}" {$attributes}/>
HTML;

        return $html;
    }

    private function convertAttributesToString(array $attributes)
    {
        $string = ' ';
        foreach ($attributes as $attributeName => $attributeValue) {
            $string .= sprintf('%s="%s" ', $attributeName, $attributeValue);
        }

        return $string;
    }
}