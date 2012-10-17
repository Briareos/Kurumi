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
        $loginError = null;
        $lastUsername = null;

        if ($session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $loginError = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        if ($session && $session->has(SecurityContext::LAST_USERNAME)) {
            $lastUsername = $session->get(SecurityContext::LAST_USERNAME);
        }

        $templateFile = 'PageBundle:Front:front_page.html.twig';
        $templateParams = array(
            'login_error' => $loginError,
            'last_username' => $lastUsername,
            'form_register' => $registerForm->createView(),
            'geocoded' => $geocoded,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($loginError) {
                return $this->ajaxHelper->renderAjaxForm('UserBundle:Form:login_form.html.twig', $templateParams);
            } else {
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $this->router->generate('front'));
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}