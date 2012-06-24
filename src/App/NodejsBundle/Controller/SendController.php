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

class SendController extends Controller
{

    /**
     * @Route("chat/send", name="chat_send")
     */
    public function sendAction()
    {
        /** @var $sender User */
        $sender = $this->getUser();

        /** @var $userManager \App\UserBundle\Entity\UserManager */
        $userManager = $this->get('user_manager');

        $receiverId = $this->getRequest()->request->get('uid');
        /** @var $receiver User */
        $receiver = $userManager->find($receiverId);

        if (!$sender instanceof UserInterface || !$receiver instanceof UserInterface || $sender->isEqualTo($receiver)) {
            throw new AccessDeniedException();
        }

        $messageText = $this->getRequest()->request->get('message');

        /** @var $nodejs Nodejs */
        $nodejs = $this->get('nodejs');

        $message = new ChatMessage();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setText($messageText);

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);

        if ($receiver->getChatCache()->getActive() && $receiver->getChatCache()->getActive()->isEqualTo($sender)) {
            $chatUser = $em->getRepository('App\NodejsBundle\Entity\ChatUser')->findOneBy(array(
                'sender' => $sender,
                'receiver' => $receiver,
            ));
            $chatUser->setLast($message);
            $em->persist($chatUser);
        }
        $em->flush();

        $messageData = array(
            'command' => 'message',
            'tid' => $this->getRequest()->request->get('tid'),
            'sender' => array(
                'u' => (int)$sender->getId(),
                'n' => $sender->getName(),
                'p' => 'http://loopj.com/images/facebook_32.png',
            ),
            'receiver' => array(
                'u' => (int)$receiver->getId(),
                'n' => $receiver->getName(),
                'p' => 'http://loopj.com/images/facebook_32.png',
            ),
            'message' => array(
                'i' => (int)$message->getId(),
                't' => $message->getCreated()->getTimestamp(),
                'b' => $message->getText(),
            )
        );

        $nodejsMessageToSender = new NodejsMessage('chat');
        $nodejsMessageToSender->setData($messageData);
        $nodejsMessageToSender->setChannel('nodejs_user_' . $sender->getId());
        $nodejs->send($nodejsMessageToSender);

        $nodejsMessageToReceiver = new NodejsMessage('chat');
        $nodejsMessageToReceiver->setData($messageData);
        $nodejsMessageToReceiver->setChannel('nodejs_user_' . $receiver->getId());
        $nodejs->send($nodejsMessageToReceiver);

        return new Response();
    }
}