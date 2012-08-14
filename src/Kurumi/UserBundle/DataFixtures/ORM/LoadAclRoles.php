<?php

namespace Kurumi\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Briareos\AclBundle\Entity\AclRole;

class LoadAclRoles extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $administrator = new AclRole();
        $administrator->setName('administrator');
        $administrator->setInternalRole(AclRole::ADMINISTRATOR);
        $this->addReference('role-administrator', $administrator);
        $manager->persist($administrator);

        $authenticatedUser = new AclRole();
        $authenticatedUser->setName('authenticated_user');
        $authenticatedUser->setInternalRole(AclRole::AUTHENTICATED_USER);
        $this->addReference('role-authenticated_user', $authenticatedUser);
        $manager->persist($authenticatedUser);

        $anonymousUser = new AclRole();
        $anonymousUser->setName('anonymous_user');
        $anonymousUser->setInternalRole(AclRole::ANONYMOUS_USER);
        $this->addReference('role-anonymous_user', $anonymousUser);
        $manager->persist($anonymousUser);

        $manager->flush();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    function setContainer(ContainerInterface $container = null)
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