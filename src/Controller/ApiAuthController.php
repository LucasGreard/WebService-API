<?php

namespace App\Controller;

use App\Entity\Client;
use App\Exception\ResourceValidationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use OpenApi\Annotations as OA;
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
     *     description="Returns a api token to connect (Need your API KEY)"
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="API key is invalid or does not exist"
     *     )
     * )
     */
    public function apiAuth(ManagerRegistry $doctrine, Request $request)
    {
        try {
            if (!$request->headers->get("authorization"))
                throw new ResourceValidationException("Enter your Bearer Token");

            $auth = explode("Bearer ", $request->headers->get("authorization"))[1];

            $user = $doctrine->getRepository(Client::class)->findOneBy(['api_key' => $auth]);
            $emailClient = $doctrine->getRepository(Client::class)->findClientByKey($auth);

            if (!$user)
                throw new ResourceValidationException("API key is invalid or does not exist");

            return $this->json([
                "Token" =>  $this->createJWT($emailClient[0]['email']),
                "Expiration time (in seconds)" => 3600,
                "Type" => "Bearer",
                "Email" => $emailClient[0]['email']
            ], 200);
        } catch (ResourceValidationException $e) {
            return $this->json([
                "error" => $e->getMessage()
            ], 400);
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

    private function isExpirationToken($tokenDecoded)
    {
        try {
            $now = time();
            $tokenArray = json_decode(json_encode($tokenDecoded), true);

            if ($now > $tokenArray['exp'])
                throw new ResourceValidationException("The API key has expired, log in again to get a new one");
        } catch (ResourceValidationException $e) {
            return $this->json([
                "error" => $e->getMessage(),
                "_link" => "https://127.0.0.1:8000/v1/api/auth"
            ], 400);
        }
    }

    public function valideToken()
    {
        if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER))
            throw new ResourceValidationException("You are not authorized");
        $key = "secure_coding";
        $jwt = preg_split("/ /", $_SERVER['HTTP_AUTHORIZATION'])[1];

        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $this->isExpirationToken($decoded);
        return json_decode(json_encode($decoded), true);
    }
}
