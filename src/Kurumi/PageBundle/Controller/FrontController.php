<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Kurumi\UserBundle\Form\Type\RegisterFormType;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;

class FrontController extends Controller
{

    /**
     * @Route("/front", name="front_page")
     */
    public function frontAction()
    {
        $defaultUser = new User();
        $defaultProfile = new Profile();
        $defaultCity = new City();
        $defaultProfile->setCity($defaultCity);
        $defaultUser->setProfile($defaultProfile);

        $registerForm = $this->createForm(new RegisterFormType(), $defaultUser);

        return $this->render('PageBundle:Front:front.html.twig', array(
            'form_register' => $registerForm->createView(),
        ));
    }
}