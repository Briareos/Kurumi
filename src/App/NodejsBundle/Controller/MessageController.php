<?php

namespace App\NodejsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\DBAL\Connection;

class MessageController extends Controller {

    /**
     * @Route("nodejs/message", name="nodejs_message")
     */
    public function messageAction() {
        $nodejsSettings = $this->container->getParameter('nodejs.settings');
        $request = $this->getRequest();
        if($nodejsSettings['serviceKey'] !== $request->query->get('serviceKey', '')) {
            throw new AccessDeniedException('Invalid node.js service key provided.');
        }

        /** @var $connection Connection */
        $connection = $this->getDoctrine()->getConnection();
        $qb = $connection->createQueryBuilder();
        $qb->select('s.id')->from('session','s');

        $response = new Response(json_encode(array(
            'authToken' => '',
            'nodejsValidAuthToken' => true,
            'channels' => array(),
            'presenceUids' => array(),

        )));
        //$response->headers->set('Content-Type', 'application/json');
        $response->headers->set('NodejsServiceKey', $nodejsSettings['serviceKey']);
        return $response;
    }
}