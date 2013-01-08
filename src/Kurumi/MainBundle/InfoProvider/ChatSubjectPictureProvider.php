<?php

namespace Kurumi\MainBundle\InfoProvider;

use Briareos\ChatBundle\Subject\PictureProviderInterface;
use Kurumi\MainBundle\InfoProvider\PictureInfoProvider;
use Kurumi\MainBundle\InfoProvider\ProfilePictureProvider;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;

class ChatSubjectPictureProvider implements PictureProviderInterface
{
    private $profilePictureProvider;

    private $pictureInfoProvider;

    private $filterName;

    public function __construct(ProfilePictureProvider $profilePictureProvider, PictureInfoProvider $pictureInfoProvider, $filterName)
    {
        $this->profilePictureProvider = $profilePictureProvider;
        $this->pictureInfoProvider = $pictureInfoProvider;
        $this->filterName = $filterName;
    }

    public function getSubjectPicture(ChatSubjectInterface $subject)
    {
        /** @var $subject \Kurumi\MainBundle\Entity\User */
        $profile = $subject->getProfile();
        if ($profile !== null) {
            $picture = $this->profilePictureProvider->getPicture($profile);

            return $this->pictureInfoProvider->getPictureUrl($picture, $this->filterName);
        }

        return null;
    }

}