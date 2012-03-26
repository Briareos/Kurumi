<?php

namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class LoginController extends Controller {

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        return array(
            'error' => $error,
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction() {
        // Will never be executed, Symfony intercepts the request.
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        // Will never be executed, Symfony intercepts the request.
    }
}