<?php

namespace App\Controller;

use App\Entity\Buyer;
use App\Entity\Client;
use DateTime;
use DateTimeImmutable;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations\Get;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\TokenController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * 
     */
    //@Route("/api/connect/google", name="connect_google_start")
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to google!
        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * 
     */
    //@Route("/api/connect/google/check", name="connect_google_check")
    public function connectCheckAction(ClientRegistry $clientRegistry, ManagerRegistry $doctrine, TokenController $token)
    {
        $accessGoogle = $clientRegistry->getClient('google');

        $tokenAccess = $accessGoogle->getAccessToken();

        $token->setToken($tokenAccess);

        dd($token->getToken());

        $dataGoogle = $accessGoogle->fetchUserFromToken($tokenAccess)
            ->toArray();

        $this->userExist($dataGoogle, $doctrine);

        return $this->render(
            'client/default.html.twig',
            [
                'tokenAccess' => $tokenAccess
            ]
        );
    }

    private function userExist($dataGoogle, $doctrine)
    {
        $user = $doctrine->getRepository(Client::class)->findOneBy(['email' => $dataGoogle['email']]);
        if (!$user) {
            $user = new Client();
            $user->setFullname($dataGoogle['name'])
                ->setEmail($dataGoogle['email'])
                ->setCreatedAt(new \DateTime);
            $doctrine->getManager()->persist($user);
            $doctrine->getManager()->flush();
        }
        return $user;
    }
}
