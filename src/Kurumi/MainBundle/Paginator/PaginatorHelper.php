<?php

namespace Kurumi\MainBundle\Paginator;

use Doctrine\ORM\Query;
use Zend\Paginator;

class PaginatorHelper
{
    protected function getPaginator($totalResults, $page, $itemsPerPage, $pageRange)
    {
        $paginatorAdapter = new Paginator\Adapter\Null($totalResults);
        $paginator = new Paginator\Paginator($paginatorAdapter);
        $paginatorScrollingStyle = new Paginator\ScrollingStyle\Sliding();
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);
        $paginator->setPageRange($pageRange);
        $paginator->setDefaultScrollingStyle($paginatorScrollingStyle);
    }

    public function applyOffsetAndLimit(Query $query, Paginator\Paginator $paginator)
    {
        $query->setFirstResult(($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage())
            ->setMaxResults($paginator->getItemCountPerPage());
    }
}
