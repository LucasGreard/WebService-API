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
use Symfony\Component\HttpFoundation\Request;

class ApiAuthController extends AbstractController
{
    /**
     * @Get(
     *     path = "v1/api/auth",
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
    public function apiAuth(ManagerRegistry $doctrine, Request $requet)
    {
        $auth = explode("Bearer ", $requet->headers->get("authorization"))[1];

        $user = $doctrine->getRepository(Client::class)->findOneBy(['api_key' => $auth]);
        $emailClient = $doctrine->getRepository(Client::class)->findClientByKey($auth);
        // dd($emailClient);
        try {
            if (!$user)
                throw new ResourceValidationException("La clé API est invalide ou n'existe pas");

            return $this->json([
                "Token" =>  $this->createJWT($emailClient[0]['email']),
                "Expiration time (in seconds)" => 3600,
                "Type" => "Bearer",
                $emailClient[0]
            ], 200);
        } catch (ResourceValidationException $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
    private function createJWT($emailClient)
    {
        $now_seconds = time();
        $key = "secure_coding";
        $payload = [

            "iss" => $emailClient,
            "aud" => "https://127.0.0.1:8000/v1/api/doc",
            "iat" => $now_seconds,
            "exp" => $now_seconds + (60 * 60)
        ];
        header("Content-Type: application/json");
        return JWT::encode($payload, $key);
    }
    public function isExpirationToken($tokenDecoded)
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
    public function valideToken()
    {
        if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER))
            throw new ResourceValidationException("Vous n'êtes pas autorisé");
        $key = "secure_coding";
        $jwt = preg_split("/ /", $_SERVER['HTTP_AUTHORIZATION'])[1];

        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $this->isExpirationToken($decoded);
        return json_decode(json_encode($decoded), true);
    }
}
