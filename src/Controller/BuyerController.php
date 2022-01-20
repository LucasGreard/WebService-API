<?php

namespace App\Controller;

use App\Entity\Buyer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use App\Exception\ResourceValidationException;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations\Get;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BuyerController extends AbstractController
{
    /**
     * @Post(
     *     path = "/api/buyers",
     *     name = "app_buyers_post"
     * )
     * @View(statusCode=201)
     * 
     * @QueryParam(
     *   name="Param"
     * )
     * @OA\Response(
     *     response=201,
     *     description="Post a buyer"
     *     )
     * )
     * @ParamConverter("buyer", converter="fos_rest.request_body")
     * @param Buyer $buyer
     */
    public function postBuyer(ManagerRegistry $doctrine, Request $request, Buyer $buyer)
    {
        // dd($request->getContent());
        // dd($buyer);
        try {
            // $test = json_decode($request->getContent(), true);
            // dd($test);
            $em = $doctrine->getManager();
            $em->persist($buyer);
            $em->flush();
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }

    /**
     * @Get(
     *     path = "v1/api/buyer/{id}",
     *     name = "app_buyer_get"
     * )
     * @View(statusCode=200)
     * 
     * @QueryParam(
     *   name="Param"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a buyer with details"
     *     )
     * )
     */
    public function getBuyer(ManagerRegistry $doctrine, $id)
    {
        if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {

            http_response_code(401);

            die;
        }

        $key = "secure_coding";

        try {

            $jwt = preg_split("/ /", $_SERVER['HTTP_AUTHORIZATION'])[1];

            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $buyer = $doctrine->getRepository(Buyer::class)->returnBuyer($id);
            return $this->json($buyer, 200);
        } catch (ResourceValidationException $exception) {

            return $this->json(["error" => $e->getMessage()], 400);
        }
        // try {
        //     if (!$id || $id < 1 || is_int($id))
        //         throw new ResourceValidationException("La valeur de l'id n'est pas bonne, id doit être un entier strictement supérieur à 1");

        //     $buyer = $doctrine->getRepository(Buyer::class)->returnBuyer($id);
        //     // dd($buyer);
        //     return $this->json($buyer, 200);
        // } catch (ResourceValidationException $e) {
        //     return $this->json(["error" => $e->getMessage()], 400);
        // }
    }
}
