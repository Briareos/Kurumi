<?php

namespace App\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\UserBundle\Entity\UserManager;
use App\UserBundle\Entity\User;

class LoadUsers extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var $userManager UserManager */
        $userManager = $this->container->get('user_manager');
        $admin = new User();
        $admin->setEmail("gmefox@gmail.com");
        $admin->setPlainPassword("metalfox");
        $userManager->updatePassword($admin);
        $admin->setTimezone('Europe/Belgrade');
        $admin->setName("Fox");
        /** @var $administratorRole \Briareos\AclBundle\Entity\AclRole */
        $administratorRole = $this->getReference('role-administrator');
        $administratorRole->addUser($admin);
        $manager->persist($admin);
        $manager->persist($administratorRole);

        $manager->flush();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 10;
    }


}