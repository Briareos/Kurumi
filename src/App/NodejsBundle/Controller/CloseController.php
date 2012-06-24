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

class CloseController extends Controller
{

    /**
     * @Route("chat/close", name="chat_close")
     */
    public function closeAction()
    {
        /** @var $sender User */
        $sender = $this->getUser();

        $tid = $this->getRequest()->request->get('tid', '');

        if (empty($tid) || !is_string($tid)) {
            throw new AccessDeniedException('Parameter "tid" is required and must be a string.');
        }

        if (!$sender->getChatCache() instanceof ChatCache) {
            throw new AccessDeniedException('User doesn\'t have an assigned ChatCache entity.');
        }

        /** @var $userManager \App\UserBundle\Entity\UserManager */
        $userManager = $this->get('user_manager');

        $receiverId = $this->getRequest()->request->get('uid');
        /** @var $receiver User */
        $receiver = $userManager->find($receiverId);

        if (!$sender instanceof UserInterface || !$receiver instanceof UserInterface || $sender->isEqualTo($receiver)) {
            throw new AccessDeniedException();
        }

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        /** @var $result \Doctrine\DBAL\Driver\Statement */
        $result = $connection->executeQuery('
            SELECT cm.id FROM chat__message cm
            WHERE (cm.receiver_id = :receiver_id AND cm.sender_id = :sender_id) OR (cm.receiver_id = :sender_id AND cm.sender_id = :receiver_id)
            ORDER BY cm.id DESC LIMIT 1
            ', array(
            ':sender_id' => $receiver->getId(),
            ':receiver_id' => $sender->getId(),
        ));
        $lastMessageId = $result->fetchColumn();
        if ($lastMessageId) {
            $connection->executeUpdate('
                UPDATE chat__user cu
                SET cu.clear_id = :clear_id
                WHERE cu.sender_id = :sender_id AND cu.receiver_id = :receiver_id
                ', array(
                ':clear_id' => $lastMessageId,
                ':sender_id' => $receiver->getId(),
                ':receiver_id' => $sender->getId(),
            ));
        }

        /** @var $nodejs Nodejs */
        $nodejs = $this->get('nodejs');
        $closeMessage = new NodejsMessage('chat');
        $closeMessage->setData(array(
            'command' => 'close',
            'tid' => $tid,
            'uid' => $receiver->getId(),
        ));
        $closeMessage->setChannel('nodejs_user_' . $sender->getId());
        $nodejs->send($closeMessage);

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        if ($sender->getChatCache()->getActive() && $sender->getChatCache()->getActive()->isEqualTo($receiver)) {
            $sender->getChatCache()->setActive(null);
        }
        $sender->getChatCache()->removeOpen($receiver->getId());
        $em->flush();

        return new Response();
    }
}