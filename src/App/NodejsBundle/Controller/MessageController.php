<?php

namespace App\NodejsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\DBAL\Connection;

class MessageController extends Controller
{

    /**
     * @Route("nodejs/message", name="nodejs_message")
     */
    public function messageAction()
    {
        /** @var $nodejs \App\NodejsBundle\Util\Nodejs */
        $nodejs = $this->get('nodejs');
        $request = $this->getRequest();

        if ($nodejs->getServiceKey() !== $request->request->get('serviceKey', '')) {
            throw new AccessDeniedException('Invalid node.js service key provided.');
        }
        $message = json_decode($request->request->get('messageJson'));

        switch ($message->messageType) {
            case 'authenticate':
                /** @var $connection Connection */
                $connection = $this->getDoctrine()->getConnection();
                $query = $connection->executeQuery('SELECT s.user_id FROM session s WHERE MD5(s.session_id) = :auth_token ORDER BY s.session_time DESC LIMIT 1', array(
                    ':auth_token' => $message->authToken,
                ));
                $userId = $query->fetchColumn();

                $channels = array();
                if($userId) {
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

    private function getPresenceUids($userId) {
        /** @var $connection Connection */
        $connection = $this->getDoctrine()->getConnection();
        $presenceQuery = $connection->executeQuery('SELECT u.id FROM user u WHERE u.id <> :current_user_id',array(
            ':current_user_id'=>$userId,
        ));
        $presenceUids = $presenceQuery->fetchAll(\PDO::FETCH_COLUMN);
        return $presenceUids;
    }
}