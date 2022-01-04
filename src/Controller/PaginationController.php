<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;


class PaginationController extends AbstractController
{
    private function createPagerFanta($data)
    {
        $adapter = new ArrayAdapter($data);
        return new Pagerfanta($adapter);
    }

    public function paginate($data, $limit, $page)
    {
        if ($page == null)
            $page = 1;

        $pagerfanta = $this->createPagerFanta($data);
        $pagerfanta->setMaxPerPage($limit);
        if ($page > $pagerfanta->getNbPages())
            throw new ExceptionController('La limite de page est de ' . $pagerfanta->getNbPages());

        $pagerfanta->setCurrentPage($page);
        return $pagerfanta->getCurrentPageResults();
    }

    public function nbPage($data, $limit)
    {
        $pagerfanta = $this->createPagerFanta($data);
        $pagerfanta->setMaxPerPage($limit);
        return $pagerfanta->getNbPages();
    }

    public function getModelPagination($limit, $nbPage, $page)
    {
        return [
            "paginate : ",
            [
                "limit" => $limit,
                "number of page" => $nbPage,
                "current page" => $page
            ]
        ];
    }
}
