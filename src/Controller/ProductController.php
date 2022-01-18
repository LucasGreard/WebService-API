<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use App\Controller\PaginationController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use App\Exception\ResourceValidationException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "/api/product/{id}",
     *     name = "app_product_get"
     * )
     * @View(statusCode=200)
     * 
     * @QueryParam(
     *   name="Param"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a product with details"
     *     )
     * )
     */
    public function getProduct(ManagerRegistry $doctrine, $id)
    {

        try {
            if (!$id || $id < 1 || is_int($id))
                throw new ResourceValidationException("La valeur de l'id n'est pas bonne, id doit être un entier strictement supérieur à 1");

            $product = $doctrine->getRepository(Product::class)->returnProduct($id);
            return $this->json($product, 200);
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }

    /**
     * @Get(
     *     path = "/api/products&limit={limit}&page={page}",
     *     name = "app_products_get",
     * )
     * @View
     * @QueryParam(
     *   name="limit"
     * )
     * @QueryParam(
     *   name="page"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all product with details (We need a limit and a page)"
     *     )
     * )
     */
    public function getProducts(ManagerRegistry $doctrine, $limit, $page, PaginationController $paginate)
    {
        try {

            if (!$limit || $limit < 1 || is_int($limit))
                throw new ResourceValidationException("La valeur de limit n'est pas bonne");

            if (!$page || $page < 1 || is_int($page))
                throw new ResourceValidationException("La valeur de page n'est pas bonne");

            $products = $doctrine->getRepository(Product::class)->findAll();
            $paginate = new PaginationController();
            $result = $paginate->paginate($products, $limit, $page);
            $nbPage = $paginate->nbPage($products, $limit);

            if (!$result)
                throw new ResourceValidationException("Aucune donnée");

            return $this->json([
                $paginate->getModelPagination($limit, $page, $nbPage),
                $result
            ], 200,);
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
}
