<?php

namespace Briareos\NodejsBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NodejsController extends ContainerAware
{

    /**
     * @Route("/nodejs/message", name="nodejs_message")
     */
    public function messageAction()
    {
        /** @var $nodejs \App\NodejsBundle\Util\Nodejs */
        $nodejs = $this->container->get('nodejs');
        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->container->get('request');

        if ($nodejs->getServiceKey() !== $request->request->get('serviceKey', '')) {
            throw new AccessDeniedException('Invalid node.js service key provided.');
        }
        $message = json_decode($request->request->get('messageJson'));

        switch ($message->messageType) {
            case 'authenticate':
                /** @var $connection \Doctrine\DBAL\Connection */
                $connection = $this->container->get('doctrine.orm.default_entity_manager')->getConnection();
                $query = $connection->executeQuery('SELECT s.user_id FROM session s WHERE MD5(s.session_id) = :auth_token ORDER BY s.session_time DESC LIMIT 1', array(
                    ':auth_token' => $message->authToken,
                ));
                $userId = $query->fetchColumn();

                $channels = array();
                if ($userId) {
                    $channels[] = "nodejs_user_$userId";
                    $presenceUserIds = $this->getPresenceUids($userId);
                } else {
                    $channels[] = "nodejs_user_0";
                    $presenceUserIds = array();
                }

                $data = array(
                    'serviceKey' => $nodejs->getServiceKey(),
                    'authToken' => $message->authToken,
                    'clientId' => $message->clientId,
                    'nodejsValidAuthToken' => (bool)$userId,
                    'channels' => $channels,
                    'presenceUids' => $presenceUserIds,
                    'uid' => (int)$userId,
                    'contentTokens' => isset($message->contentTokens) ? $message->contentTokens : array(),
                );
                break;
            case 'userOffline':
                // $message['uid'] has went offline, or just refreshed his browser.
                break;
            default:
                throw new AccessDeniedException(sprintf('Invalid message type: %s', $message->messageType));
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('NodejsServiceKey', $nodejs->getServiceKey());
        return $response;
    }
}