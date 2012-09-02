<?php

namespace Kurumi\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Briareos\AjaxBundle\Ajax;
use Kurumi\SearchBundle\Form\Type\SearchPeopleFormType;
use DoctrineExtensions\Paginate\Paginate;

class SearchController extends Controller
{

    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("user_picture_provider")
     *
     * @var \Kurumi\UserBundle\User\PictureProvider
     */
    private $userPictureProvider;

    /**
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @DI\Inject("knp_paginator")
     *
     * @var \Knp\Component\Pager\Paginator
     */
    private $paginator;

    /**
     * @Route("/search", name="search")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function searchAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();

        $searchPeopleForm = $this->createForm(new SearchPeopleFormType(), $user->getProfile());

        if ($this->getRequest()->isMethod('post')) {
            $searchPeopleForm->bind($this->getRequest());
            if ($searchPeopleForm->isValid()) {
                $this->em->persist($user->getProfile());
                $this->em->flush();
            }
        }

        $qb = $this->em->createQueryBuilder();
        $parameters = array(
            ':unit' => 6371,
        );

        $qb->from('UserBundle:User', 'u');
        $qb->addSelect('u As user');
        $qb->innerJoin('u.profile', 'p');
        $qb->innerJoin('p.city', 'c');
        $qb->addSelect('(:unit * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(c.latitude)) * COS(RADIANS(c.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(c.latitude)))) As distance');
        $qb->addOrderBy('distance', 'asc');
        $qb->where($qb->expr()->neq('u.id', $user->getId()));

        if ($user->getProfile()->getLookingInCity()) {
            $parameters[':latitude'] = $user->getProfile()->getLookingInCity()->getLatitude();
            $parameters[':longitude'] = $user->getProfile()->getLookingInCity()->getLongitude();
        } elseif ($user->getProfile()->getCity()) {
            $parameters[':latitude'] = $user->getProfile()->getCity()->getLatitude();
            $parameters[':longitude'] = $user->getProfile()->getCity()->getLongitude();
        } else {
            throw new \Exception("No city specified.");
        }


        //$paginator = $this->paginator->paginate($qb->getQuery(), $this->getRequest()->query->getInt('page', 1), 10);
        $page = $this->getRequest()->query->getInt('page', 1);

        $totalResults = Paginate::getTotalQueryResults($qb->getQuery());
        $qb->setParameters($parameters);
        $results = $qb->setFirstResult(0)->setMaxResults(12)->getQuery()->execute();


        /** @var $nodejsAuthenticator \Briareos\NodejsBundle\Nodejs\Authenticator */
        $templateFile = 'SearchBundle:Search:search_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'results' => $results,
            'total_results' => $totalResults,
            'form' => $searchPeopleForm->createView(),
            'user_picture' => $this->userPictureProvider,
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams), $this->router->generate('search')));
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

}