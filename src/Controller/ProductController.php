<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use App\Controller\PaginationController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "/product/{id}",
     *     name = "app_product_get",
     *     requirements = {"id"="\d+"}
     * )
     * @View(statusCode=200)
     * 
     * @QueryParam(
     *   name="Param"
     * )
     */
    public function getProduct(ManagerRegistry $doctrine, int $id)
    {
        $product = $doctrine->getRepository(Product::class)->returnProduct($id);
        if (!$product)
            return $this->json("not found", 404);
        return $this->json($product, 200);
    }

    /**
     * @Get(
     *     path = "/products&limit={limit}&page={page}",
     *     name = "app_products_get",
     * )
     * @View
     * @QueryParam(
     *   name="limit"
     * )
     * @QueryParam(
     *   name="page"
     * )
     */
    public function getProducts(ManagerRegistry $doctrine, $limit, $page)
    {
        $products = $doctrine->getRepository(Product::class)->findAll();
        $paginate = new PaginationController();
        $result = $paginate->paginate($products, $limit, $page);
        $nbPage = $paginate->nbPage($products, $limit);
        if (!$result)
            return $this->json("Aucune donnée", 400);

        return $this->json([
            $result,
            [
                "paginate : ",
                [
                    "limit" => $limit,
                    "number of page" => $nbPage,
                    "current page" => $page
                ]
            ]
        ], 200,);
    }
}
