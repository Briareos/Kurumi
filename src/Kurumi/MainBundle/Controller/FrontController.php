<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Locale\Locale;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Kurumi\MainBundle\Form\Type\RegisterFormType;
use Briareos\AjaxBundle\Ajax;
use Kurumi\MainBundle\Entity\User;
use Kurumi\MainBundle\Entity\Profile;
use Kurumi\MainBundle\Entity\City;
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
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @Route("/front", name="front_page")
     * @Method("GET")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function frontAction(Request $request)
    {
        $defaultUser = new User();
        $defaultProfile = new Profile();
        $defaultCity = new City();
        $defaultProfile->setCity($defaultCity);
        $defaultUser->setProfile($defaultProfile);

        $city = $request->server->get('GEOIP_CITY');
        $countryCode = $request->server->get('GEOIP_COUNTRY_CODE');

        if ($city && $countryCode) {
            $country = Locale::getDisplayCountries(\Locale::getDefault())[$countryCode];
            $defaultCity->setCountryName($country);
            $defaultCity->setName($city);
        }

        $registerForm = $this->createForm(new RegisterFormType(), $defaultUser);

        $session = $this->session;
        $loginError = null;
        $lastUsername = null;

        if ($session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $loginError = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        if ($session && $session->has(SecurityContext::LAST_USERNAME)) {
            $lastUsername = $session->get(SecurityContext::LAST_USERNAME);
        }

        $templateFile = ':Front:front.html.twig';
        $templateParams = array(
            'login_error' => $loginError,
            'last_username' => $lastUsername,
            'form_register' => $registerForm->createView(),
            'detected_city' => $city,
        );

        if ($request->isXmlHttpRequest()) {
            if ($loginError) {
                return $this->ajaxHelper->renderAjaxForm(':Form:user_login_front.html.twig', $templateParams);
            } else {
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $this->router->generate('front'));
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}