<?php

namespace Application\Sonata\MediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;

class LoadGalleryHasMedias extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $genders = array(
            'unknown',
            'male',
            'female',
        );
        foreach ($genders as $genderName) {
            $i = 0;
            $genderGallery = $this->getReference(sprintf('gallery-gender_%s', $genderName));
            while ($this->hasReference(sprintf('media-avatar_%s_%s', $genderName, $i))) {
                $avatarMedia = $this->getReference(sprintf('media-avatar_%s_%s', $genderName, $i));
                $galleryHasMedia = new GalleryHasMedia();
                $galleryHasMedia->setGallery($genderGallery);
                $galleryHasMedia->setMedia($avatarMedia);
                $galleryHasMedia->setEnabled(true);
                $manager->persist($galleryHasMedia);
                $i++;
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    function getOrder()
    {
        return 5;
    }

}