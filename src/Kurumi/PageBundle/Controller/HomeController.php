<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;

class HomeController extends Controller
{

    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

    /**
     * @Route("/home", name="home_page")
     */
    public function homeAction()
    {
        $user = $this->getUser();
        /** @var $nodejsAuthenticator \Briareos\NodejsBundle\Nodejs\Authenticator */
        $nodejsAuthenticator = $this->get('nodejs.authenticator');
        $nodejsAuthenticator->authenticate($this->getRequest()->getSession(), $user);
        $templateFile = 'PageBundle:Home:home_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'nodejs_auth_token' => $nodejsAuthenticator->generateAuthToken($this->getRequest()->getSession(), $user),
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