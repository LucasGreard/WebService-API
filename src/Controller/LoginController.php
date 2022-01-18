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
use Symfony\Component\HttpFoundation\Response;



class LoginController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/api/connect/google", name="connect_google_start")
     */
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
     * @Route("/api/connect/google/check", name="connect_google_check")
     */
    public function connectCheckAction(ClientRegistry $clientRegistry, ManagerRegistry $doctrine)
    {
        //Vérifier si un client avec l'adresse email existe
        //Si Oui, le connecter avec User
        //Si Non, le créer en bdd

        $dataGoogle = $clientRegistry->getClient('google')
            ->fetchUser()
            ->toArray();
        //Créer un token valable xtime 
        //Ensuite, afficher une page panel avec le token
        $x = $this->userExist($dataGoogle, $doctrine);
        
        return $this->redirectToRoute('app_clientPanel_get');

        // $clientRegistry->getClient('google')->getAccessToken(); //Créer et récupère un token de google
    }
    private function userExist($dataGoogle, $doctrine)
    {
        //MAnque une table email pour Client
        //Manque la connexion 
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
    /**
     * @Route("/client", name="app_clientPanel_get")
     */
    public function clientPanel(): Response
    {
        return $this->render(
            'client/default.html.twig'
        );
    }
}
