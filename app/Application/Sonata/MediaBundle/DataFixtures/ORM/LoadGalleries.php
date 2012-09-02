<?php

namespace Application\Sonata\MediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\MediaBundle\Entity\Gallery;

class LoadGalleries extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
            $genderGallery = new Gallery();
            $genderGallery->setContext('user_picture');
            $genderGallery->setEnabled(true);
            $genderGallery->setName(sprintf("Gender: %s", $genderName));
            $genderGallery->setDefaultFormat('user_picture_medium');
            $this->addReference(sprintf('gallery-gender_%s', $genderName), $genderGallery);
            $manager->persist($genderGallery);
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