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
     *     path = "/products&limit={limit}",
     *     name = "app_products_get",
     * )
     * @View
     * @QueryParam(
     *   name="limit"
     * )
     */
    public function getProducts(ManagerRegistry $doctrine, $limit)
    {

        $products = $doctrine->getRepository(Product::class)->findAll();
        $offset = 3;
        $paginate = new PaginationController();
        $result = $paginate->paginate($products, $limit, $offset);
        // return new JsonResponse(["data" => $result]);


        if (!$result)
            return $this->json("Aucune donnée", 400);
        return $this->json($result, 200);
    }
}
