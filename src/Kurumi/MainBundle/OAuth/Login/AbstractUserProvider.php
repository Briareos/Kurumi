<?php

namespace Kurumi\MainBundle\OAuth\Login;

use Doctrine\ORM\EntityManager;
use Kurumi\MainBundle\CityFinder\CityFinderInterface;
use Kurumi\MainBundle\Entity\City;
use Kurumi\MainBundle\Entity\OAuth;
use Kurumi\MainBundle\Entity\Profile;
use Kurumi\MainBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

abstract class AbstractUserProvider
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Kurumi\MainBundle\CityFinder\CityFinderInterface
     */
    protected $cityFinder;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setCityFinder(CityFinderInterface $cityFinder)
    {
        $this->cityFinder = $cityFinder;
    }

    public function findUserByEmail($email)
    {
        $userRepository = $this->em->getRepository('KurumiMainBundle:User');
        return $userRepository->findOneBy([
            'email' => $email,
        ]);
    }

    public function findOAuthById($oauthId)
    {
        return $this->getRepository()->findOneBy([
            'oauthId' => $oauthId,
            'name' => $this->getName(),
        ]);
    }

    public function findOAuthEntityByEmail($email)
    {
        return $this->getRepository()->findOneBy([
            'email' => $email,
        ]);
    }

    public function getRepository()
    {
        return $this->em->getRepository('KurumiMainBundle:OAuth');
    }

    public function createOAuth(UserResponseInterface $response, User $user)
    {
        $oauth = new OAuth();
        $oauth->setUser($user);
        $oauth->setEmail($this->getUserEmailByResponse($response));
        $oauth->setName($this->getName());
        $oauth->setOauthId($this->getOAuthIdByResponse($response));
        $this->em->persist($oauth);
        $this->em->flush();
        return $oauth;
    }

    public function findOAuthUserByResponse(UserResponseInterface $response)
    {
        $oauth = $this->findOAuthByResponse($response);
        if ($oauth === null) {
            return null;
        }
        return $oauth->getUser();
    }

    public function findOAuthByResponse(UserResponseInterface $response)
    {
        return $this->findOAuthById($this->getOAuthIdByResponse($response));
    }

    public function findUserByResponse(UserResponseInterface $response)
    {
        return $this->findUserByEmail($this->getUserEmailByResponse($response));
    }

    public function findCity($name)
    {
        /** @var $city \Kurumi\MainBundle\Entity\City */
        $city = null;
        $newCity = $this->cityFinder->find(new City(), $name);
        if ($newCity) {
            if ($existingCity = $this->em->getRepository('KurumiMainBundle:City')->findOneBy([
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
            ])
            ) {
                $city = $existingCity;
            } else {
                $city = $newCity;
            }
        }
        return $city;
    }

    public function createUser(UserResponseInterface $response)
    {
        $user = new User();
        $this->fillUserInfo($user, $response);
        $profile = new Profile();
        $profile->setUser($user);
        $city = $this->findCity($this->getCityName($response));
        if ($city !== null) {
            $profile->setCity($city);
        }
        $this->em->persist($user);
        return $user;
    }

    abstract public function getName();

    /**
     * @param \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface $response
     * @return string
     */
    abstract public function getUserEmailByResponse(UserResponseInterface $response);

    /**
     * @param \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface $response
     * @return string
     */
    abstract public function getOAuthIdByResponse(UserResponseInterface $response);

    abstract public function fillUserInfo(User $user, UserResponseInterface $response);

    abstract public function fillProfileInfo(Profile $profile, UserResponseInterface $response);

    abstract public function getCityName(UserResponseInterface $response);

    abstract public function isVerifiedEmail(UserResponseInterface $response);

}
