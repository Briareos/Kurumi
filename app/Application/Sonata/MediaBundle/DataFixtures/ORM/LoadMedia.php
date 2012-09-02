<?php

namespace Application\Sonata\MediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Finder\Finder;

class LoadMedia extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $avatarFinder = new Finder();

        $genders = array(
            'unknown' => __DIR__ . '/../../Resources/avatar/unknown/',
            'male' => __DIR__ . '/../../Resources/avatar/male/',
            'female' => __DIR__ . '/../../Resources/avatar/female/',
        );

        foreach ($genders as $genderName => $avatarDirectory) {
            $i = 0;
            /** @var $avatarFile \Symfony\Component\Finder\SplFileInfo */
            foreach ($avatarFinder->in($avatarDirectory)->files() as $avatarFile) {
                $avatarMedia = new Media();
                $avatarMedia->setBinaryContent($avatarFile);
                $avatarMedia->setEnabled(true);
                $avatarMedia->setContext('user_picture');
                $avatarMedia->setProviderName('sonata.media.provider.image');
                $manager->persist($avatarMedia);
                $this->addReference(sprintf('media-avatar_%s_%s', $genderName, $i), $avatarMedia);
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
        return 6;
    }

}