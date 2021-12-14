<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "/product/{id}",
     *     name = "app_product_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function showProduct(ManagerRegistry $doctrine, int $id)
    {
        $product = $doctrine->getRepository(Product::class)->returnProduct($id);
        return $product;
    }

    /**
     * @Get(
     *     path = "/products",
     *     name = "app_products_show",
     * )
     * @View
     */
    public function showProducts(ManagerRegistry $doctrine)
    {

        $product = $doctrine->getRepository(Product::class)->findAll();
        $adapter = new ArrayAdapter($product);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPageResults = $pagerfanta->getCurrentPageResults();
        return $currentPageResults;
    }
}
