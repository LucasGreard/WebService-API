<?php

namespace App\Controller;

use App\Entity\Buyer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use App\Exception\ResourceValidationException;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Entity\Client;

class BuyerController extends AbstractController
{
    /**
     * @Post(
     *     path = "/buyers",
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
     * @param Buyer $buyer
     */
    public function postBuyer(ManagerRegistry $doctrine, Request $request)
    {

        // try {
        //     if (!$id || $id < 1 || is_int($id))
        //         throw new ResourceValidationException("La valeur de l'id n'est pas bonne, id doit être un entier strictement supérieur à 1");

        //     $product = $doctrine->getRepository(Product::class)->returnProduct($id);
        //     return $this->json($product, 200);
        // } catch (ResourceValidationException $e) {
        //     return $this->json(["error" => $e->getMessage()], 400);
        // }
        try {
            $x = new Buyer();
            $c = new Client();
            $test = json_decode($request->getContent(), true);
            $x->setFullname($test['fullname'])
                ->setClient($c->getId())
                ->setCountry($test['country_id'])
                ->setCreatedAt(new \DateTime);
            $em = $doctrine->getManager();
            $em->persist($x);
            $em->flush();
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
}
