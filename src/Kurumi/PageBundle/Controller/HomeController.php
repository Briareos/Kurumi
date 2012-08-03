<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Briareos\AjaxBundle\Ajax\AjaxResponse;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax\Command\AjaxCommandPage;
use Briareos\AjaxBundle\Ajax\Command\AjaxCommandState;

class HomeController extends Controller
{

    /**
     * @DI\Inject("twig")
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @Route("/home", name="home_page")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
        $user = $this->getUser();
        $templateFile = 'PageBundle:Home:home_page.html.twig';
        $templateParams = array(
            'user' => $user,
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $template = $this->twig->loadTemplate($templateFile);
            $commands[] = new AjaxCommandPage($template->renderBlock('title', $templateParams), $template->renderBlock('body', $templateParams));
            $commands[] = new AjaxCommandState(array('uri' => $this->get('router')->generate('home_page')));
            return new AjaxResponse($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}