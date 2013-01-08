<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\MainBundle\Controller\ProfileController;
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
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        $user = $this->getUser();
        $templateFile = ':Home:home.html.twig';
        $templateParams = array(
            'user' => $user,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams), $this->router->generate('front')));
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}