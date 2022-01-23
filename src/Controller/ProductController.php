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
use Doctrine\SqlFormatter\Token;
use Firebase\JWT\JWT;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Token\AccessToken;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     */
    public function getProduct(ManagerRegistry $doctrine, $id, ApiAuthController $tokenController)
    {
        try {
            if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER))
                throw new ResourceValidationException("Vous n'êtes pas autorisé");

            if (!$id || $id < 1 || is_int($id))
                throw new ResourceValidationException("La valeur de l'id n'est pas bonne, id doit être un entier strictement supérieur à 1");
            $key = "secure_coding";
            $jwt = preg_split("/ /", $_SERVER['HTTP_AUTHORIZATION'])[1];

            $decoded = JWT::decode($jwt, $key, array('HS256'));

            $tokenController->isValidateToken($decoded);

            $product = $doctrine->getRepository(Product::class)->returnProduct($id);
            if (empty($product))
                throw new ResourceValidationException("Aucune donnée avec cette id");
            return $this->json($product, 200);
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
     */
    public function getProducts(ManagerRegistry $doctrine, $limit, $page, PaginationController $paginate, ApiAuthController $tokenController)
    {

        try {
            if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {

                throw new ResourceValidationException("Vous n'êtes pas autorisé");
            }
            if (!$limit || $limit < 1 || is_int($limit))
                throw new ResourceValidationException("La valeur de limit n'est pas bonne");

            if (!$page || $page < 1 || is_int($page))
                throw new ResourceValidationException("La valeur de page n'est pas bonne");

            $key = "secure_coding";
            $jwt = preg_split("/ /", $_SERVER['HTTP_AUTHORIZATION'])[1];

            $decoded = JWT::decode($jwt, $key, array('HS256'));

            $tokenController->isValidateToken($decoded);

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
