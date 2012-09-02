<?php

namespace Kurumi\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Kurumi\UserBundle\Entity\Profile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kurumi\UserBundle\Entity\UserManager;
use Kurumi\UserBundle\Entity\User;
use Faker;

class LoadProfiles extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        /** @var $admin User */
        $admin = $this->getReference('user_admin');
        $adminProfile = new Profile();
        $adminProfile->setUser($admin);
        $manager->persist($adminProfile);

        $cities = array();
        $c = 1;
        while ($this->hasReference(sprintf('city_%s', $c))) {
            $cities[] = $this->getReference(sprintf('city_%s', $c));
            $c++;
        }

        $i = 1;
        while ($this->hasReference(sprintf('user_%s', $i))) {
            $faker = Faker\Factory::create();
            /** @var $user User */
            $user = $this->getReference(sprintf('user_%s', $i));
            $profile = new Profile();
            $profile->setUser($user);
            $profile->setBirthday($faker->dateTimeBetween('-50 years', '-16 years'));
            $profile->setFirstName($faker->firstName);
            $profile->setLastName($faker->lastName);
            $profile->setGender($faker->boolean(90) ? rand(1, 2) : null);
            if ($faker->boolean(90)) {
                $profile->setCity($cities[rand(0, count($cities) - 1)]);
            }
            $profile->setLookingAgedFrom($faker->boolean(90) ? rand(16, 50) : null);
            $profile->setLookingAgedTo($faker->boolean(95) ? rand($profile->getLookingAgedFrom(), 50) : null);
            $lookingFor = null;
            if ($faker->boolean(90)) {
                if ($profile->getGender() === null) {
                    $lookingFor = $faker->boolean(90) ? rand(1, 2) : 3;
                } else {
                    if ($faker->boolean(90)) {
                        if ($faker->boolean(80)) {
                            // Straight.
                            $lookingFor = ($profile->getGender() === 1) ? 2 : 1;
                        } else {
                            // Gay.
                            $lookingFor = $profile->getGender();
                        }
                    } else {
                        $lookingFor = 3;
                    }
                }
            }
            $profile->setLookingFor($lookingFor);
            $manager->persist($profile);
            $i++;
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
        return 4;
    }


}