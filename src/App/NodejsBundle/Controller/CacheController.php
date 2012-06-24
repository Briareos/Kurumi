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

class CacheController extends Controller
{

    /**
     * @Route("chat/cache", name="chat_cache")
     */
    public function cacheAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        /* {
        * u : This user's uid
        * n : This user's name
        * p : This user's picture
        * a : Active window ID
        * v : Array of open window IDs
        * o : List of online chat partners, indexed by uid
        *  {
        *   u : Partner's uid
        *   n : Partner's name
        *   p : Partner's picture
        *   s : Partner's status
        *  }
        * w : Chat windows, indexed by uid
        *  {
        *   d : Partner's data
        *    {
        *     u : Partner's uid
        *     n : Partner's name
        *     p : Partner's picture
        *     s : Partner's status
        *    }
        *   m : Messages, indexed by cmid
        *    {
        *     i : Message cmid
        *     r : 1 if it's a received message, 0 otherwise
        *     t : Message time (UNIX timestamp)
        *     b : Message body
        *    }
        *   e : Number of new messages
        *  }
        * }
        */

        $this->generateChatCache($user);

        /** @var $userManager \App\UserBundle\Entity\UserManager */
        $userManager = $this->get('user_manager');

        $picture = $userManager->getPicture($user);

        $data = array(
            'u' => $user->getId(),
            'n' => $user->getName(),
            'p' => 'http://loopj.com/images/facebook_32.png',
            'a' => $user->getChatCache()->getActiveId(),
            'v' => $user->getChatCache()->getOpen(),
            'o' => $this->getPresentUsers($user),
            'w' => array(
                21 => array(
                    'd' => array(
                        'u' => 21,
                        'n' => "Foxy",
                        'p' => 'http://loopj.com/images/facebook_32.png',
                        's' => 1,
                    ),
                    'm' => array(
                        1 => array(
                            'i' => 1,
                            'r' => 1,
                            't' => time(),
                            'b' => 'lorem ipsum nesto ' . rand(1, 1999),
                        ),
                    ),
                    'e' => 1,
                ),
            ),
            'w' => array(),
        );

        foreach($data['v'] as $openId) {
            $data['w'][$openId] = array(
                'd' => array(),
                'm' => array(),
                'e' => 0
            );
        }

        $messages = $this->getMessages($user);
        foreach($messages as $message) {
            if (!isset($data['w'][$message->partner_id])) {
                $data['w'][$message->partner_id] = array(
                    'd' => array(),
                    'm' => array(),
                    'e' => 0
                );
            }
            $messageText = $message->text;
            $data['w'][$message->partner_id]['m'][$message->id] = array(
                'i' => $message->id,
                'r' => $message->received,
                't' => strtotime($message->created),
                'b' => $messageText
            );
            if ($message->new) {
                $data['w'][$message->partner_id]['e']++;
            }
        }

        foreach ($data['w'] as $partnerId => &$window) {
            /** @var $partner User */
            $partner = $userManager->find($partnerId);
            $cacheKey = array_search($partnerId, $data['v']);
            if ($cacheKey === false) {
                $cache['v'][] = $partner->getId();
            }
            $window['d'] = array(
                'u' => $partner->getId(),
                'n' => $partner->getName(),
                'p' => 'http://loopj.com/images/facebook_32.png',
                's' => 1
            );
        }

        $open = $user->getChatCache()->getOpen();
        $newWindows = array_diff($data['v'], $open);
        if (!empty($newWindows)) {
            $newOpen = array_merge($open, $newWindows);
            $user->getChatCache()->setOpen($newOpen);
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $response;
    }

    public function generateChatCache(User $user)
    {
        if (null === $user->getChatCache()) {
            $chatCache = new ChatCache($user);
            $user->setChatCache($chatCache);
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            $em->persist($chatCache);
        }
    }

    public function getPresentUsers(User $user)
    {
        /** @var $userManager \App\UserBundle\Entity\UserManager */
        $userManager = $this->get('user_manager');
        $presentUsers = $userManager->findAll();
        $presence = array();
        foreach ($presentUsers as $presentUser) {
            if ($presentUser->isEqualTo($user)) {
                continue;
            }
            $presence[$presentUser->getId()] = array(
                'u' => $presentUser->getId(),
                'n' => $presentUser->getName(),
                'p' => 'http://loopj.com/images/facebook_32.png',
                's' => 1,
            );
        }
        return $presence;
    }

    public function getMessages(User $user)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        /** @var $result \Doctrine\DBAL\Driver\Statement */
        $result = $connection->executeQuery('SELECT cm.id, u.id AS partner_id, cm.text, cm.created,
          (cm.sender_id = u.id) AS received,
          ((cu.last_id < cm.id OR cu.last_id IS NULL) AND :user_id != cm.sender_id) AS new
          FROM chat__message cm
          LEFT JOIN user u ON IF(:user_id = cm.sender_id, u.id = cm.receiver_id, u.id = cm.sender_id)
          LEFT JOIN chat__user cu ON cu.receiver_id = :user_id AND cu.sender_id = u.id
          WHERE (cm.id > cu.clear_id OR cu.clear_id IS NULL)
          AND (cm.receiver_id = :user_id OR cm.sender_id = :user_id)
          ORDER BY cm.id ASC', array(
            ':user_id' => $user->getId(),
        ));
        $messages = $result->fetchAll(\PDO::FETCH_CLASS);
        return $messages;
    }
}