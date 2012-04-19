<?php

namespace App\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\UserBundle\Form\Type\RegisterFormType;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\City;

class FrontController extends Controller {

    /**
     * @Route("/front", name="front_page")
     * @Template()
     */
    public function frontAction() {
        $defaultUser = new User();
        $defaultProfile = new Profile();
        $defaultCity = new City();
        $defaultProfile->setCity($defaultCity);
        $defaultUser->setProfile($defaultProfile);

        $registerForm = $this->createForm(new RegisterFormType(), $defaultUser);

        return array(
            'form_register' => $registerForm->createView(),
        );
    }
}