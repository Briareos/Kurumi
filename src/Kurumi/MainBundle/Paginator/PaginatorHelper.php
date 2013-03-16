<?php

namespace Kurumi\MainBundle\Paginator;

use Doctrine\ORM\Query;
use Zend\Paginator;

class PaginatorHelper
{
    /**
     * @param $totalResults
     * @param $page
     * @param $itemsPerPage
     * @param $pageRange
     * @param string $scrollingStyle
     *   All|Elastic|Jumping|Sliding
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator($totalResults, $page, $itemsPerPage = 10, $pageRange = 10, $scrollingStyle = 'Sliding')
    {
        $paginatorAdapter = new Paginator\Adapter\Null($totalResults);
        $paginator = new Paginator\Paginator($paginatorAdapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);
        $paginator->setPageRange($pageRange);
        $paginator->setDefaultScrollingStyle($scrollingStyle);
        return $paginator;
    }

    public function applyOffsetAndLimit(Query $query, Paginator\Paginator $paginator)
    {
        $query->setFirstResult(($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage())
            ->setMaxResults($paginator->getItemCountPerPage());
    }
}
