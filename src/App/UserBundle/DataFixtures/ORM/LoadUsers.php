<?php

namespace App\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\UserBundle\Entity\UserManager;

class LoadUsers implements FixtureInterface, ContainerAwareInterface {

    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $defaultRole = new Role();
        $defaultRole->setName('ROLE_USER');
        $manager->persist($defaultRole);

        $superAdminRole = new Role();
        $superAdminRole->setName('ROLE_SUPER_ADMIN');
        $manager->persist($superAdminRole);

        $admin = new User();
        $admin->setEmail("gmefox@gmail.com");
        $admin->setPlainPassword("metalfox");
        $this->container->get('user_manager')->updatePassword($admin);
        $admin->setTimezone('Europe/Belgrade');
        $admin->setName("Fox");
        $admin->addUserRole($defaultRole);
        $admin->addUserRole($superAdminRole);
        $manager->persist($admin);

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
}