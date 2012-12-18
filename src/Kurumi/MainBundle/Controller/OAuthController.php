<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Kurumi\MainBundle\OAuth\Login\UserProvider;

class OAuthController extends Controller
{
    /**
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @Route("/oauth/failure", name="oauth_failure")
     */
    public function failureAction()
    {
        $error = $this->session->get(SecurityContext::AUTHENTICATION_ERROR);
        $this->session->remove(SecurityContext::AUTHENTICATION_ERROR);

        switch ($error) {

        }

        return $this->render(
            ':OAuth:failure.html.twig',
            array(
                'error' => $error,
            )
        );
    }

    /**
     * @Route("/oauth/success", name="oauth_success")
     */
    public function successAction()
    {
        return $this->render(':OAuth:success.html.twig');
    }
}