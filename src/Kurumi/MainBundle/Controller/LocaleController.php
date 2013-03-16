<?php

namespace Kurumi\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LocaleController extends Controller
{

    /**
     * @var \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
     *
     * @Inject("form.csrf_provider")
     */
    private $csrfProvider;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     *
     * @Inject("session")
     */
    private $session;

    /**
     * @Route("/locale/{locale}/{token}", name="switch_locale")
     */
    public function switchAction($locale, $token, Request $request)
    {
        if (!$this->csrfProvider->isCsrfTokenValid('switch_locale', $token)) {
            throw new AccessDeniedHttpException();
        }

        $enabledLocales = $this->container->getParameter('enabled_locales');

        if (!in_array($locale, $enabledLocales)) {
            return $this->createNotFoundException();
        }

        $this->get('session')->set('_locale', $locale);
        $request->setLocale($locale);

        $referrer = $request->headers->get('referer');
        if (empty($referrer)) {
            $url = $this->generateUrl('front');
        } else {
            $url = $referrer;
        }

        return $this->redirect($url);
    }
}
