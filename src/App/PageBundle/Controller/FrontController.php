<?php

namespace App\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use App\UserBundle\Form\Type\RegisterFormType;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\City;

class FrontController extends Controller
{

    /**
     * @Route("/front", name="front_page")
     */
    public function frontAction()
    {
        var_dump($this->getDoctrine()->getEntityManager()->getClassMetadata('App\UserBundle\Entity\User'));

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