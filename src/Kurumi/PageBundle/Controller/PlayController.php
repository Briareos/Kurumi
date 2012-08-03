<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;

class PlayController extends Controller
{

    /**
     * @DI\Inject("twig")
     *
     * @var \Twig_Environment
     */
    private $twig;


    /**
     * @Route("/play", name="play")
     */
    public function playAction()
    {
        $user = $this->getUser();
        $templateFile = 'PageBundle:Play:play_page.html.twig';
        $templateParams = array(
            'user' => $user,
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $data = array();
            $template = $this->twig->loadTemplate($templateFile);
            $data['page'] = array(
                'title' => $template->renderBlock('title', $templateParams),
                'body' => $template->renderBlock('body', $templateParams),
            );
            return new Response(json_encode($data), 200, array(
                'Content-Type' => 'application/json',
            ));
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}