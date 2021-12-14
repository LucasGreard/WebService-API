<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "/product/{id}",
     *     name = "app_product_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View(statusCode=200)
     */
    public function showProduct(ManagerRegistry $doctrine, int $id)
    {
        $product = $doctrine->getRepository(Product::class)->returnProduct($id);
        if (!$product) {
            return new Response("Produit non trouvé", Response::HTTP_NOT_FOUND);
        }
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
        if (!$product) {
            return new Response("Produits non trouvé", Response::HTTP_NOT_FOUND);
        }
        return $product;
    }
}
