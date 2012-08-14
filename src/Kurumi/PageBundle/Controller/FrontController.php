<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Kurumi\UserBundle\Form\Type\RegisterFormType;
use Briareos\AjaxBundle\Ajax;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;
use JMS\DiExtraBundle\Annotation as DI;

class FrontController extends Controller
{
    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

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

        /** @var $geocoder \Geocoder\Geocoder */
        $geocoder = $this->get('bazinga_geocoder.geocoder');
        $geocoded = $geocoder->using('yahoo')->geocode($this->getRequest()->getClientIp());

        if ($geocoded->getCountry() && $geocoded->getCity()) {
            $defaultCity->setCountryName($geocoded->getCountry());
            $defaultCity->setName($geocoded->getCity());
        }

        $registerForm = $this->createForm(new RegisterFormType(), $defaultUser);

        $session = $this->getRequest()->getSession();
        $loginError = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        $session->remove(SecurityContext::AUTHENTICATION_ERROR);

        $templateFile = 'PageBundle:Front:front_page.html.twig';
        $templateParams = array(
            'login_error' => $loginError,
            'form_register' => $registerForm->createView(),
            'geocoded' => $geocoded,
            'nodejs_auth_token' => false,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = array();
            $commands[] = new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams));
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}