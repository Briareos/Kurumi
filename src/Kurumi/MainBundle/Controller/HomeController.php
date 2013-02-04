<?php

namespace Kurumi\MainBundle\Controller;

use Doctrine\ORM\EntityManager;
use DoctrineExtensions\Paginate\Paginate;
use Kurumi\MainBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\MainBundle\Controller\ProfileController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use Zend\Paginator;

class HomeController extends Controller
{

    /**
     * @DI\Inject("doctrine")
     *
     * @var EntityManager
     */
    private $em;

    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var Ajax\Helper
     */
    private $ajaxHelper;

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        $user = $this->getUser();

        $pictureRepository = $this->em->getRepository('KurumiMainBundle:Picture');

        $qb = $pictureRepository->createQueryBuilder('p');
        $qb->where('p.pictureType = :picture_type');
        $qb->setParameter('picture_type', Picture::PUBLIC_PICTURE);
        $totalResults = Paginate::getTotalQueryResults($qb->getQuery());

        $page = $this->getRequest()->query->getInt('page', 1);
        $itemsPerPage = 30;
        $pageRange = 5;
        $paginatorAdapter = new Paginator\Adapter\Null($totalResults);
        $paginator = new Paginator\Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);
        $paginator->setPageRange($pageRange);
        //$paginatorScrollingStyle = new Paginator\ScrollingStyle\Sliding();
        //$paginatorView = $paginator->getPages($paginatorScrollingStyle);

        $pictures = $qb
          ->setFirstResult(($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage())
          ->setMaxResults($paginator->getItemCountPerPage())
          ->getQuery()
          ->execute();

        $templateFile = ':Home:home.html.twig';
        $templateParams = [
            'pictures' => $pictures,
        ];

        if ($this->getRequest()->isXmlHttpRequest()) {
            $url = $this->generateUrl('home');

            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}