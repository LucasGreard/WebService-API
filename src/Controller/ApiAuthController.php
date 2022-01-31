<?php

namespace App\Controller;

use App\Entity\Client;
use App\Exception\ResourceValidationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
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
     *     description="Returns a api token to connect (Need your API KEY and your email)"
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Client is invalid or does not exist or Username or Password are blank"
     *     )
     * )
     */
    public function apiAuth(ManagerRegistry $doctrine, Request $request)
    {
        try {
            $userEmail = $request->headers->get("php-auth-user");
            $pw = $request->headers->get("php-auth-pw");
            if (!$userEmail || !$pw)
                throw new ResourceValidationException("Username or Password are blank");
            $user = $doctrine->getRepository(Client::class)->findClientByUsernameAndApikey($userEmail, $pw);
            if (!$user)
                throw new ResourceValidationException("Client is invalid or does not exist");

            return $this->json([
                "Token" =>  $this->createJWT($user[0]['email']),
                "Expiration time (in seconds)" => 3600,
                "Type" => "Bearer",
                "Email" => $user[0]['email']
            ], 200);
        } catch (ResourceValidationException $e) {
            return $this->json([
                "error" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Post(
     *     path = "v1/api/auth",
     *     name = "api_auth_post"
     * )
     * @View(statusCode=200)
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a api token to connect (Need your API KEY and your email) In the body"
     *     )
     * )
     * @OA\Parameter(
     *     name="Authentification",
     *     in="query",
     *     description="use like this in body : { 'email' : 'monemail@gmail.com', 'api_key' : 'myApiKey' } ",
     *     @OA\Schema(type="json")
     * )
     * @OA\Response(
     *     response=400,
     *     description="Client is invalid or does not exist or Username or Password are blank"
     *     )
     * )
     * @param $jsonAuth
     */
    public function apiAuthPost(ManagerRegistry $doctrine, Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || $data = null)
                throw new ResourceValidationException("JSON missing, read documentation at /v1/api/doc");

            $userEmail = $data['email'];
            $pw = $data['api_key'];
            if (!$userEmail || !$pw || !$data)
                throw new ResourceValidationException("Username or Password are blank");
            $user = $doctrine->getRepository(Client::class)->findClientByUsernameAndApikey($userEmail, $pw);
            if (!$user)
                throw new ResourceValidationException("Client is invalid or does not exist");

            return $this->json([
                "Token" =>  $this->createJWT($user[0]['email']),
                "Expiration time (in seconds)" => 3600,
                "Type" => "Bearer",
                "Email" => $user[0]['email']
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
            "aud" => "/v1/api/doc",
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
                "_link" => "/v1/api/auth"
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
