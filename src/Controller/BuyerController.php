<?php

namespace App\Controller;

use App\Entity\Buyer;
use App\Entity\Client;
use App\Exception\ResourceValidationException;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Firebase\JWT\JWT;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BuyerController extends AbstractController
{
    /**
     * @Post(
     *     path = "v1/api/buyers",
     *     name = "app_buyers_post"
     * )
     * @View(statusCode=201)
     * 
     * @OA\Response(
     *     response=201,
     *     description="Post a buyer"
     *     )
     * )
     * @ParamConverter("buyer", converter="fos_rest.request_body")
     * @param Buyer $buyer
     */
    public function postBuyer(ManagerRegistry $doctrine, Request $request, Buyer $buyer, ApiAuthController $tokenController)
    {
        try {

            $tokenDecoded = $tokenController->valideToken();
            $client = $doctrine->getRepository(Client::class)->findClientId($tokenDecoded['iss']);
            $buyer->setClient($client)
                ->setCreatedAt(new DateTime('now'));

            $em = $doctrine->getManager();

            $em->persist($buyer);
            $em->flush();
            return $this->json($buyer, 201);
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
     * @OA\Response(
     *     response=200,
     *     description="Returns a buyer with details (Need authentification)"
     *     )
     * )
     */
    public function getBuyer(ManagerRegistry $doctrine, $id, ApiAuthController $tokenController)
    {

        try {
            $tokenDecoded = $tokenController->valideToken();

            $client = $doctrine->getRepository(Client::class)->findClientId($tokenDecoded['iss']);
            if (!$id || $id < 1 || is_int($id))
                throw new ResourceValidationException("La valeur de l'id n'est pas bonne, id doit être un entier strictement supérieur à 1");
            $buyer = $doctrine->getRepository(Buyer::class)->findBy([
                'id' => $id,
                'client' => $client
            ]);
            if (empty($buyer))
                throw new ResourceValidationException("Aucune donnée avec cette id");
            return $this->json($this->resultBuyerJson($buyer), 200);
        } catch (ResourceValidationException $e) {

            return $this->json(["error" => $e->getMessage()], 400);
        }
    }

    /**
     * @Get(
     *     path = "v1/api/buyers&limit={limit}&page={page}",
     *     name = "app_buyers_get"
     * )
     * @View(statusCode=200)
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns all buyers (We need a limit and a page)(Need authentification)"
     *     )
     * )
     */
    public function getBuyers(ManagerRegistry $doctrine, $limit, $page, ApiAuthController $tokenController)
    {

        try {
            $tokenDecoded = $tokenController->valideToken();
            if (!$limit || $limit < 1 || is_int($limit))
                throw new ResourceValidationException("La valeur de limit n'est pas bonne");

            if (!$page || $page < 1 || is_int($page))
                throw new ResourceValidationException("La valeur de page n'est pas bonne");

            $client = $doctrine->getRepository(Client::class)->findClientId($tokenDecoded['iss']);

            $buyer = $doctrine->getRepository(Buyer::class)->findBy([
                'client' => $client
            ]);
            $paginate = new PaginationController();
            $result = $paginate->paginate($buyer, $limit, $page);
            $nbPage = $paginate->nbPage($buyer, $limit);
            if (!$result)
                throw new ResourceValidationException("Aucune donnée");

            return $this->json([
                $paginate->getModelPagination($limit, $page, $nbPage),
                $this->resultBuyerJson($result)
            ], 200,);
        } catch (ResourceValidationException $e) {

            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
    /**
     * @Delete(
     *     path = "v1/api/buyer/{id}",
     *     name = "app_buyers_delete"
     * )
     * @View(statusCode=200)
     * 
     * @OA\Response(
     *     response=200,
     *     description="Delete a buyer (Need authentification)"
     *     )
     * )
     */
    public function deleteBuyers(ManagerRegistry $doctrine, $id, ApiAuthController $tokenController)
    {
        try {
            $tokenDecoded = $tokenController->valideToken();
            if (!$id || $id < 1 || is_int($id))
                throw new ResourceValidationException("La valeur de id n'est pas bonne");

            $client = $doctrine->getRepository(Client::class)->findClientId($tokenDecoded['iss']);

            $repository = $doctrine->getRepository(Buyer::class);
            $buyer = $repository->findOneBy([
                'id' => $id,
                'client' => $client
            ]);
            if (!$buyer || empty($buyer))
                throw new ResourceValidationException("Aucun buyer avec cette id");

            $entityManager = $doctrine->getManager();
            $entityManager->remove($buyer);
            $entityManager->flush();
            return $this->json($buyer, 200,);
        } catch (ResourceValidationException $e) {

            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
    private function resultBuyerJson($buyer)
    {
        return [
            "Buyer : ", [
                'Id' => $buyer[0]->getId(),
                'Fullname' => $buyer[0]->getfullname(),
                'Created_At' => $buyer[0]->getCreatedAt(),
                'City' => $buyer[0]->getCity(),
                'Postal Code' => $buyer[0]->getPostalCode(),
                'Age (years)' => $buyer[0]->getAge()
            ]
        ];
    }
}
