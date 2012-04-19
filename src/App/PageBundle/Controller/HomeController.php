<?php

namespace App\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller {

    /**
     * @Route("/home", name="home_page")
     * @Template()
     */
    public function homeAction() {

        return array(
        );
    }
}