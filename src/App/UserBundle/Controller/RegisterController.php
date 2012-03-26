<?php

namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Form\RegisterFormType;
use App\UserBundle\Entity\User;

class RegisterController extends Controller {

    /**
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction(Request $request) {
        $defaultUser = new User();

        $form = $this->createForm(new RegisterFormType(), $defaultUser);

        if($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if($form->isValid()) {
                $user = $form->getData();

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

}