<?php

namespace App\Controller;

use App\Exception\ResourceValidationException;
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
        try {
            if ($page == null)
                $page = 1;

            $pagerfanta = $this->createPagerFanta($data);
            $pagerfanta->setMaxPerPage($limit);
            if ($page > $pagerfanta->getNbPages())
                throw new ResourceValidationException('The page limit is ' . $pagerfanta->getNbPages());

            $pagerfanta->setCurrentPage($page);
            return $pagerfanta->getCurrentPageResults();
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
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
                "Limit" => $limit,
                "Page n°" => $nbPage,
                "Number of page" => $page
            ]
        ];
    }
}
