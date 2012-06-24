<?php

namespace App\NodejsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\UserBundle\Entity\User;
use App\NodejsBundle\Entity\ChatCache;
use App\NodejsBundle\Entity\ChatMessage;
use App\NodejsBundle\Util\Nodejs;
use App\NodejsBundle\Util\NodejsMessage;

class PingController extends Controller {

    /**
     * @Route("chat/ping", name="chat_ping")
     */
    public function pingAction() {

        return new Response();
    }
}