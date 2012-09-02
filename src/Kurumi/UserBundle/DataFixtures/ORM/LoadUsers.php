<?php

namespace Kurumi\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kurumi\UserBundle\Entity\UserManager;
use Kurumi\UserBundle\Entity\User;
use Faker;

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
        /** @var $administratorRole \Briareos\AclBundle\Entity\AclRole */
        $administratorRole = $this->getReference('role-administrator');

        $admin = new User();
        $admin->setEmail("gmefox@gmail.com");
        $admin->setPlainPassword("metalfox");
        $admin->setTimezone('Europe/Belgrade');
        $admin->setName("Fox");
        $admin->addAclRole($administratorRole);
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        for ($i = 1; $i <= 5000; $i++) {
            $faker = Faker\Factory::create();
            $user = new User();
            $user->setEmail(rand(1, 9999) . $faker->email);
            $user->setPlainPassword("metalfox");
            $user->setTimezone('Europe/Belgrade');
            $user->setName($faker->userName);
            $manager->persist($user);
            $this->addReference(sprintf('user_%s', $i), $user);
        }

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
        return 2;
    }


}