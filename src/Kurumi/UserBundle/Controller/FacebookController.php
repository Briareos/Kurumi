<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Kurumi\UserBundle\Entity\City;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\Facebook;
use Kurumi\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FacebookController extends Controller
{
    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @DI\Inject("facebook")
     *
     * @var \Facebook
     */
    private $facebook;

    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("city_finder")
     *
     * @var \Kurumi\UserBundle\Entity\CityFinderInterface
     */
    private $cityFinder;

    /**
     * @DI\Inject("security.context")
     *
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @Route("/facebook/connect", name="facebook_connect")
     */
    public function connectAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        $facebookUser = $this->facebook->getUser();
        // Does the user have an OAuth session?
        if ($facebookUser) {
            return $this->redirect($this->router->generate('facebook_connect_check'));
        } else {
            return $this->redirect($this->getLoginUrl());
        }
    }

    /**
     * This is a shortcut for $this->facebook->getLoginUrl(...) with prefilled parameters.
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->facebook->getLoginUrl(array(
            'scope' => $this->getScope(),
            'redirect_uri' => $this->router->generate('facebook_connect', array('profile' => 1, 'city' => 1), true),
        ));
    }

    /**
     * @Route("/facebook/connect/check", name="facebook_connect_check")
     */
    public function connectCheckAction()
    {
        $fbUser = $this->facebook->getUser();

        if (!$fbUser) {
            return $this->render('UserBundle:Facebook:connect_check_cancel.html.twig', array(
                'success' => false,
                'login_url' => $this->getLoginUrl(),
            ));
        }

        $rawPermissions = $this->facebook->api('/me/permissions');
        if (empty($rawPermissions['data'][0])) {
            $permissions = array();
        } else {
            $permissions = array_keys($rawPermissions['data'][0]);
        }

        $missingPermissions = array_diff($this->getScope(), $permissions);

        if ($missingPermissions) {
            return $this->render('UserBundle:Facebook:connect_check_incomplete.html.twig');
        } else {
            return $this->render('UserBundle:Facebook:connect_check_success.html.twig');
        }
    }

    /**
     * @Route("/facebook/connect/finalize", name="facebook_connect_finalize")
     */
    public function connectFinalizeAction()
    {
        $user = $this->getUser();
        $fbUser = $this->facebook->getUser();

        if (!$fbUser) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                return $this->ajaxHelper->location($this->router->generate('facebook_connect'));
            } else {
                return $this->redirect($this->router->generate('facebook_connect'));
            }
        }

        $fbMe = $this->facebook->api('/me');

        if (!$user instanceof UserInterface) {
            $existingUser = $this->getUserByEmail($fbMe['email']);
            $existingFacebookUser = $this->getUserByFacebookId($fbMe['id']);

            if ($existingUser) {
                $this->authenticateUser($existingUser);

                $this->session->getFlashBag()->add('success', "Welcome back!");

                return $this->redirect($this->router->generate('front'));
            } else {
                $fillProfile = $this->getRequest()->query->getInt('profile');
                $fillCity = $this->getRequest()->query->getInt('city');
                $newUser = $this->createNewUser($fbMe, $fillProfile, $fillCity);

                $this->em->persist($newUser);
                $this->em->flush();

                $this->session->getFlashBag()->add('success', "Welcome to our site!");

                return $this->redirect($this->router->generate('front'));
            }
        } else {

        }
    }

    public function getScope()
    {
        return array('email', 'user_birthday', 'user_location');
    }

    public function getUserByEmail($email)
    {
        $userRepository = $this->em->getRepository('UserBundle:User');
        return $userRepository->findOneBy(array('email' => $email));
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = $this->container->getParameter('main_firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->securityContext->setToken($token);
    }

    public function createNewUser(array $fbMe, $fillProfile = true, $fillCity = true)
    {
        $newUser = new User();

        $newFacebook = new Facebook();
        $newFacebook->setUser($newUser);
        $newFacebook->setFacebookId($fbMe['id']);

        $this->fillUser($newUser, $fbMe);

        if ($fillProfile) {
            $newProfile = new Profile();
            $newProfile->setUser($newUser);
            $this->fillProfile($newProfile, $fbMe);

            if ($fillCity) {
                $city = $this->getCity($fbMe);
                $newProfile->setCity($city);
            }
        }
        return $newUser;
    }

    public function fillUser(User $user, array $fbMe)
    {
        $user->setEmail($fbMe['email']);

        if (!empty($fbMe['first_name'])) {
            $user->setName($fbMe['first_name']);
        } else {
            $user->setName($fbMe['name']);
        }
    }

    public function fillProfile(Profile $profile, array $fbMe)
    {
        if (!empty($fbMe['birthday'])) {
            $birthday = \DateTime::createFromFormat('m/d/Y', $fbMe['birthday']);
            $profile->setBirthday($birthday);
        }
        if (!empty($fbMe['first_name'])) {
            $profile->setFirstName($fbMe['first_name']);
        }
        if (!empty($fbMe['last_name'])) {
            $profile->setLastName($fbMe['last_name']);
        }
        if (!empty($fbMe['gender'])) {
            if ($fbMe['gender'] === 'male') {
                $profile->setGender(Profile::GENDER_MALE);
            } else {
                $profile->setGender(Profile::GENDER_FEMALE);
            }
        }
    }

    public function getCity($fbMe)
    {
        if (!empty($fbMe['location']['name'])) {
            $location = $fbMe['location']['name'];
        } elseif (!empty($fbMe['hometown']['name'])) {
            $location = $fbMe['hometown']['name'];
        } else {
            $location = false;
        }
        if ($location) {
            $city = $this->findCity($location);
            return $city;
        }
        return null;
    }

    public function findCity($name)
    {
        /** @var $city \Kurumi\UserBundle\Entity\City */
        $city = null;
        $newCity = $this->cityFinder->find(new City(), $name);
        if ($newCity) {
            if ($existingCity = $this->em->getRepository('UserBundle:City')->findOneBy(array(
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
            ))
            ) {
                $city = $existingCity;
            } else {
                $city = $newCity;
            }
        }
        return $city;
    }

    private function getUserByFacebookId($fbId)
    {
        $facebookUser = $this->em->getRepository('UserBundle:Facebook')->findOneBy(array('facebookId'=>$fbId));
        if($facebookUser instanceof Facebook) {
            return $facebookUser->getUser();
        }
        return null;
    }
}
