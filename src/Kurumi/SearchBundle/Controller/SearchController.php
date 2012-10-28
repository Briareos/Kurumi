<?php

namespace Kurumi\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zend\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation as DI;
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
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

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
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @Route("/search", name="search")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function searchAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();

        $searchPeopleForm = $this->createForm(new SearchPeopleFormType(), $user->getProfile());

        $qb = $this->em->createQueryBuilder();
        $parameters = array();

        $qb->from('UserBundle:User', 'u');
        $qb->addSelect('u As user');
        $qb->addSelect('p'); // User.Profile
        $qb->addSelect('c'); // User.Profile.City
        $qb->innerJoin('u.profile', 'p');
        $qb->innerJoin('p.city', 'c');
        $qb->where($qb->expr()->neq('u.id', $user->getId())); // Exclude current user.

        // Show only users that have no age preference, or the current user falls into it.
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->isNull('p.lookingAgedFrom'),
            $qb->expr()->lte('p.lookingAgedFrom', $user->getProfile()->getAge())
        ));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->isNull('p.lookingAgedTo'),
            $qb->expr()->gte('p.lookingAgedTo', $user->getProfile()->getAge())
        ));

        // Select distance, so we can order by it.
        $qb->addSelect('(:unit * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(c.latitude)) * COS(RADIANS(c.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(c.latitude)))) As distance');
        $qb->addOrderBy('distance', 'asc');

        // Secondary order is by last activity.
        $qb->addOrderBy('u.lastActiveAt', 'desc');

        // Parameters that are set only if the distance is required (this is mainly for the count query). Users with no location set are excluded from the query.
        $parameters[':unit'] = 6371;
        $parameters[':latitude'] = $user->getProfile()->getCity()->getLatitude();
        $parameters[':longitude'] = $user->getProfile()->getCity()->getLongitude();

        // Search users older than the current user's preference.
        if ($user->getProfile()->getLookingAgedFrom()) {
            //$qb->andWhere($qb->expr()->lte('p.birthday', new \DateTime(sprintf('-%s years', $user->getProfile()->getLookingAgedFrom()))));
            $agedFrom = new \DateTime(sprintf('-%s years', $user->getProfile()->getLookingAgedFrom()));
            $qb->andWhere('p.birthday <= :aged_from');
            $qb->setParameter(':aged_from', $agedFrom);
            $parameters[':aged_from'] = $agedFrom;
        }

        // Search users younger than the current user's preference.
        if ($user->getProfile()->getLookingAgedTo()) {
            //$qb->andWhere($qb->expr()->gte('p.birthday', new \DateTime(sprintf('-%s years', $user->getProfile()->getLookingAgedTo()))));
            $agedTo = new \DateTime(sprintf('-%s years', $user->getProfile()->getLookingAgedTo()));
            $qb->andWhere('p.birthday >= :aged_to');
            $qb->setParameter(':aged_to', $agedTo);
            $parameters[':aged_to'] = $agedTo;
        }

        // Match gender search. 1 is male, 2 is female, 3 is both. Users that have no gender set are excluded from the query.
        if ($user->getProfile()->getLookingFor() === 3) {
            $qb->andWhere($qb->expr()->isNotNull('p.gender'));
        } else {
            $qb->andWhere($qb->expr()->eq('p.gender', $user->getProfile()->getLookingFor()));
        }
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('p.lookingFor', $user->getProfile()->getGender()),
            $qb->expr()->eq('p.lookingFor', 3)
        ));

        $totalResults = Paginate::getTotalQueryResults($qb->getQuery());

        $page = $this->getRequest()->query->getInt('page', 1);
        $itemsPerPage = 12;
        $pageRange = 5;
        $paginatorAdapter = new Paginator\Adapter\Null($totalResults);
        $paginator = new Paginator\Paginator($paginatorAdapter);
        $paginatorScrollingStyle = new Paginator\ScrollingStyle\Sliding();
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);
        $paginator->setPageRange($pageRange);
        $paginatorView = $paginator->getPages($paginatorScrollingStyle);

        $qb->setParameters($parameters);
        $results = $qb->setFirstResult(($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage())->setMaxResults($paginator->getItemCountPerPage())->getQuery()->execute();

        /** @var $nodejsAuthenticator \Briareos\NodejsBundle\Nodejs\Authenticator */
        $templateFile = 'SearchBundle:Search:search_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'results' => $results,
            'paginator' => $paginatorView,
            'total_results' => $totalResults,
            'form' => $searchPeopleForm->createView(),
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $pageParameters = array();
            if ($paginator->getCurrentPageNumber() !== 1) {
                $pageParameters['page'] = $paginator->getCurrentPageNumber();
            }
            $pageUrl = $this->router->generate('search', $pageParameters);
            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $pageUrl);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/search-form", name="search_form")
     * @Method("post")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function searchFormAction()
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

        if ($searchPeopleForm->isValid()) {
            return $this->redirect($this->router->generate('search', $this->ajaxHelper->getPjaxParameters()));
        } else {
            $commands = new Ajax\CommandContainer();
            $templateParams = array(
                'user' => $user,
                'form' => $searchPeopleForm->createView(),
            );
            $commands->add(new Ajax\Command\Form($this->renderView('SearchBundle:Form:search_people_form.html.twig', $templateParams)));
            return new Ajax\Response($commands);
        }
    }
}