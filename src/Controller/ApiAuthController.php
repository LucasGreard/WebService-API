<?php

namespace App\Controller;

use App\Entity\Client;
use App\Exception\ResourceValidationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\User\UserInterface;
use Firebase\JWT\JWT;

class ApiAuthController extends AbstractController
{
    /**
     * @Get(
     *     path = "v1/api/auth&token={token}",
     *     name = "api_auth_get"
     * )
     * @View(statusCode=200)
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a api token to connect"
     *     )
     * )
     */
    public function apiAuth($token, ManagerRegistry $doctrine)
    {
        $user = $doctrine->getRepository(Client::class)->findOneBy(['api_key' => $token]);
        try {
            if (!$user)
                throw new ResourceValidationException("La clé API est invalide ou n'existe pas");
            $tokenJWT = $this->createJWT();
            return $this->json([
                "Token" => $tokenJWT,
                "Expiration time" => "One hour",
                "Type" => "Bearer"
            ], 200);
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
    private function createJWT()
    {
        $now_seconds = time();
        $key = "secure_coding";
        $payload = [

            "iss" => "lucas.greard07@gmail.com",
            "aud" => "https://127.0.0.1:8000/v1/api/doc",
            "iat" => $now_seconds,
            "exp" => $now_seconds + (60 * 60)
        ];
        header("Content-Type: application/json");
        return JWT::encode($payload, $key);
    }
    public function isValidateToken($tokenDecoded)
    {
        try {
            $now = time();
            $tokenArray = json_decode(json_encode($tokenDecoded), true);

            if ($now > $tokenArray['exp'])
                throw new ResourceValidationException("La clé API a expiré, reconnectez-vous pour en avoir une nouvelle");
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
}
