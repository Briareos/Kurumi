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
use JMS\DiExtraBundle\Annotation\Inject;

class FrontController extends Controller
{
    /**
     * @var \Briareos\AjaxBundle\Ajax\Helper
     *
     * @Inject("templating.ajax.helper")
     */
    private $ajaxHelper;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     *
     * @Inject("router")
     */
    private $router;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     *
     * @Inject("session")
     */
    private $session;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     *
     * @Inject("security.context")
     */
    private $securityContext;

    /**
     * @Route("/", name="front")
     * @Method("GET")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function frontAction(Request $request)
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            if ($user->getProfile() !== null) {
                return $this->redirect($this->generateUrl('profile', ['id' => $user->getProfile()->getId()]));
            } elseif ($user->getAffiliate() !== null) {
                return $this->redirect($this->generateUrl('affiliate', ['id' => $user->getAffiliate()->getId()]));
            }
        }

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
