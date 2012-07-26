<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller {

    /**
     * @Route("/home", name="home_page")
     */
    public function homeAction() {

        return $this->render('PageBundle:Home:home_page.html.twig');
    }
}