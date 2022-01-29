<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use App\Controller\PaginationController;
use App\Exception\ResourceValidationException;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "v1/api/product/{id}",
     *     name = "app_product_get"
     * )
     * @View(statusCode=200)
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a product with details (Need authentification)"
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="You are not authorized or the value of the id is not good, id must be an integer strictly greater than 1 "
     *     )
     * )
     */
    public function getProduct(ManagerRegistry $doctrine, $id, ApiAuthController $tokenController)
    {
        try {
            $tokenController->valideToken();

            if (!$id || $id < 1 || is_int($id))
                throw new ResourceValidationException("The value of the id is not good, id must be an integer strictly greater than 1");

            $product = $doctrine->getRepository(Product::class)->returnProduct($id);
            if (empty($product))
                throw new ResourceValidationException("No data with this id");
            return $this->json($this->resultProductJson($product), 200);
        } catch (ResourceValidationException $e) {

            return $this->json(["error" => $e->getMessage()], 400);
        }
    }

    /**
     * @Get(
     *     path = "v1/api/products&limit={limit}&page={page}",
     *     name = "app_products_get",
     * )
     * @View
     * @OA\Response(
     *     response=200,
     *     description="Returns all product with details (We need a limit and a page) (Need authentification)"
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="You are not authorized or the value of the limit/page is not good, limit/page must be an integer strictly greater than 1 "
     *     )
     * )
     */
    public function getProducts(ManagerRegistry $doctrine, $limit, $page, PaginationController $paginate, ApiAuthController $tokenController)
    {

        try {
            $tokenController->valideToken();
            if (!$limit || $limit < 1 || is_int($limit))
                throw new ResourceValidationException("The limit value is not good");

            if (!$page || $page < 1 || is_int($page))
                throw new ResourceValidationException("The page value is not good");

            $products = $doctrine->getRepository(Product::class)->findAll();
            $paginate = new PaginationController();
            $result = $paginate->paginate($products, $limit, $page);
            $nbPage = $paginate->nbPage($products, $limit);

            if (!$result)
                throw new ResourceValidationException("No data");

            return $this->json([
                $paginate->getModelPagination($limit, $page, $nbPage),
                $this->resultProductsJson($result, $limit)
            ], 200,);
        } catch (ResourceValidationException $e) {

            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
    private function resultProductsJson($result, $limit)
    {
        $products = [];
        for ($i = 0; $i < $limit; $i++) {
            if (array_key_exists($i, $result)) {

                $products[] = [
                    "Product : ", [
                        'Id' => $result[$i]->getId(),
                        'Fullname' => $result[$i]->getfullname(),
                        'Model' => $result[$i]->getmodel(),
                        'Brand' => $result[$i]->getbrand()->getbrand()
                    ]
                ];
            } else {
                return $products;
            }
        }
        return $products;
    }
    private function resultProductJson($result)
    {
        return [
            "Product : ", [
                'Id' => $result[0]->getId(),
                'Fullname' => $result[0]->getfullname(),
                'Model' => $result[0]->getmodel(),
                'Brand' => $result[0]->getbrand()->getbrand(),
                'Price (Current Money)' => $result[0]->getprice(),
                'Resolution (Pixels)' => [
                    'Height' => $result[0]->getResolutionId()->getheight(),
                    'Width' => $result[0]->getResolutionId()->getwidth(),
                ],
                'Operating System' => $result[0]->getOperatingSystemId()->getoperatingSystem(),
                'Weight (g)' => $result[0]->getweight(),
                'Screen Size (inch)' => $result[0]->getScreenSize(),
                'Storage (Go)' => $result[0]->getstorage(),
                'Battery (mAh)' => $result[0]->getbattery(),
                'Ram (Go)' => $result[0]->getRAM()
            ]
        ];
    }
}
