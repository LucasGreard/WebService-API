<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;


class PaginationController extends AbstractController
{
    public function paginate($data, $limit, $offset)
    {
        if (0 == $limit || 0 == $offset) {
            throw new \LogicException('Limit & offset must be greater than 0.');
        }
        $adapter = new ArrayAdapter($data);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        return $pagerfanta->getCurrentPageResults();
    }
}
