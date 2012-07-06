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

        /** @var $administratorRole \Briareos\AclBundle\Entity\AclRole */
        $administratorRole = $this->getReference('role-administrator');

        $admin = new User();
        $admin->setEmail("gmefox@gmail.com");
        $admin->setPlainPassword("metalfox");
        $admin->setTimezone('Europe/Belgrade');
        $admin->setName("Fox");
        $admin->addAclRole($administratorRole);
        $userManager->updatePassword($admin);
        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail("gmefox@live.com");
        $user1->setPlainPassword("metalfox");
        $user1->setTimezone('Europe/Belgrade');
        $user1->setName("Gray");
        $userManager->updatePassword($user1);
        $manager->persist($user1);

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
        return 1;
    }


}