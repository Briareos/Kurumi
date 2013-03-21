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
        return [
            'profile_picture' => new \Twig_Function_Method($this, 'getProfilePicture', ['is_safe' => ['html']]),
            'picture' => new \Twig_Function_Method($this, 'getPicture', ['is_safe' => ['html']]),
        ];
    }

    public function getProfilePicture(Profile $profile, $format = null, $attributes = [])
    {
        $picture = $this->profilePictureProvider->getPicture($profile);

        return $this->getPictureHtml($picture, $format, $attributes);
    }

    public function getPicture(Picture $picture, $format = null, $attributes = [], $options = [])
    {
        return $this->getPictureHtml($picture, $format, $attributes, $options);
    }

    public function getPictureHtml(Picture $picture, $format = null, $attributes = [], $options = [])
    {
        $options += [
            'auto_dimensions' => true,
            'scale_width' => 0,
            'scale_height' => 0,
            'max_width' => 0,
            'max_height' => 0,
        ];
        $url = $this->pictureInfoProvider->getPictureUrl($picture, $format);
        if ($options['auto_dimensions']) {
            $dimensions = $this->pictureInfoProvider->getPictureDimensions($picture, $format);
            if ($dimensions !== null) {
                $heightRatio = $widthRatio = 1;
                if ($options['max_width']) {
                    if ($dimensions['width'] > $options['max_width']) {
                        $widthRatio = $dimensions['width'] / $options['max_width'];
                    }
                }
                if ($options['max_height']) {
                    if ($dimensions['height'] > $options['max_height']) {
                        $heightRatio = $dimensions['height'] / $options['max_height'];
                    }
                }

                if ($widthRatio > $heightRatio) {
                    $options['scale_width'] = $options['max_width'];
                } elseif ($heightRatio > $widthRatio) {
                    $options['scale_height'] = $options['max_height'];
                }

                if ($options['scale_width']) {
                    $ratio = $dimensions['width'] / $options['scale_width'];
                    $dimensions['width'] = $options['scale_width'];
                    $dimensions['height'] = $dimensions['height'] / $ratio;
                } elseif ($options['scale_height']) {
                    $ratio = $dimensions['height'] / $options['scale_height'];
                    $dimensions['height'] = $options['scale_height'];
                    $dimensions['width'] = $dimensions['width'] / $ratio;
                }
                $attributes += $dimensions;
            }
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
            $string .= sprintf('%s="%s" ', $attributeName, htmlentities($attributeValue, ENT_QUOTES, 'UTF-8'));
        }

        return $string;
    }
}
