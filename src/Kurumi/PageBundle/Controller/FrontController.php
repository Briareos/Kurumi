<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Kurumi\UserBundle\Form\Type\RegisterFormType;
use Kurumi\UserBundle\Form\Type\LoginFormType;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;

class FrontController extends Controller
{

    /**
     * @Route("/front", name="front_page")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function frontAction()
    {
        $defaultUser = new User();
        $defaultProfile = new Profile();
        $defaultCity = new City();
        $defaultProfile->setCity($defaultCity);
        $defaultUser->setProfile($defaultProfile);

        $cityToCityNameTransformer = $this->get('city_to_city_name_transformer');

        /** @var $geocoder \Geocoder\Geocoder */
        $geocoder = $this->get('bazinga_geocoder.geocoder');
        $geocoded = $geocoder->using('yahoo')->geocode($this->getRequest()->getClientIp());

        if ($geocoded->getCountry() && $geocoded->getCity()) {
            $defaultCity->setCountryName($geocoded->getCountry());
            $defaultCity->setName($geocoded->getCity());
        }

        $registerForm = $this->createForm(new RegisterFormType($cityToCityNameTransformer), $defaultUser);

        return $this->render('PageBundle:Front:front_page.html.twig', array(
            'form_register' => $registerForm->createView(),
            'geocoded' => $geocoded,
        ));
    }
}