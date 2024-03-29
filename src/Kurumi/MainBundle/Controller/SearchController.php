<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zend\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use Briareos\AjaxBundle\Ajax;
use Kurumi\MainBundle\Form\Type\SearchPeopleFormType;
use DoctrineExtensions\Paginate\Paginate;

class SearchController extends Controller
{
    /**
     * @var \Briareos\AjaxBundle\Ajax\Helper
     *
     * @Inject("templating.ajax.helper")
     */
    private $ajaxHelper;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     *
     * @Inject("router")
     */
    private $router;

    /**
     * @var \Doctrine\ORM\EntityManager
     *
     * @Inject("doctrine.orm.default_entity_manager")
     */
    private $em;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     *
     * @Inject("session")
     */
    private $session;

    /**
     * @Route("/search", name="search")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function searchAction()
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();

        $searchPeopleForm = $this->createForm(new SearchPeopleFormType(), $user->getProfile());

        $qb = $this->em->createQueryBuilder();
        // Holds parameters that are required only for the actual select query and can be omitted in the count query.
        $parameters = [];

        $qb->from('KurumiMainBundle:User', 'u');
        $qb->addSelect('u As user');

        // User.Profile
        $qb->innerJoin('u.profile', 'p');
        $qb->addSelect('p');

        // User.Profile.City
        $qb->innerJoin('p.city', 'c');
        $qb->addSelect('c');

        // User.Profile.ProfileCache
        $qb->leftJoin('p.cache', 'pc');
        $qb->addSelect('pc');

        // Exclude current user.
        $qb->where($qb->expr()->neq('u.id', $user->getId()));

        // Show users without a preference and those which the user falls into.
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('p.lookingAgedFrom'),
                $qb->expr()->lte('p.lookingAgedFrom', $user->getProfile()->getAge())
            )
        );
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('p.lookingAgedTo'),
                $qb->expr()->gte('p.lookingAgedTo', $user->getProfile()->getAge())
            )
        );

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
            $agedFrom = new \DateTime(sprintf('-%s years', $user->getProfile()->getLookingAgedFrom()));
            $qb->andWhere('p.birthday <= :aged_from');
            $qb->setParameter(':aged_from', $agedFrom);
            $parameters[':aged_from'] = $agedFrom;
        }

        // Search users younger than the current user's preference.
        if ($user->getProfile()->getLookingAgedTo()) {
            $agedTo = new \DateTime(sprintf('-%s years', $user->getProfile()->getLookingAgedTo()));
            $qb->andWhere('p.birthday >= :aged_to');
            $qb->setParameter(':aged_to', $agedTo);
            $parameters[':aged_to'] = $agedTo;
        }

        // Match gender search. 1 is male, 2 is female, 3 is both. Users that have no gender set are excluded.
        if ($user->getProfile()->getLookingFor() === 3) {
            $qb->andWhere($qb->expr()->isNotNull('p.gender'));
        } else {
            $qb->andWhere($qb->expr()->eq('p.gender', $user->getProfile()->getLookingFor()));
        }

        // Reverse the gender match.
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('p.lookingFor', $user->getProfile()->getGender()),
                $qb->expr()->eq('p.lookingFor', 3)
            )
        );

        // Generate the count query before binding the parameters that are not needed for it.
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

        // Finally, bind the parameters required for ordering.
        $qb->setParameters($parameters);

        $results = $qb
            ->setFirstResult(($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage())
            ->setMaxResults($paginator->getItemCountPerPage())
            ->getQuery()
            ->execute();

        $templateFile = ':Search:search.html.twig';
        $templateParams = [
            'user' => $user,
            'results' => $results,
            'paginator' => $paginatorView,
            'total_results' => $totalResults,
            'form' => $searchPeopleForm->createView(),
        ];

        if ($this->getRequest()->isXmlHttpRequest()) {
            $pageParameters = [];
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
        /** @var $user \Kurumi\MainBundle\Entity\User */
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
            $templateParams = [
                'user' => $user,
                'form' => $searchPeopleForm->createView(),
            ];

            return $this->ajaxHelper->renderAjaxForm(':Form:search_people.html.twig', $templateParams);
        }
    }
}
