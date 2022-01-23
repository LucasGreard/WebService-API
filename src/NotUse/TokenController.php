<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TokenController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setToken($token)
    {
        $this->session->set('test', $token);
    }
    // private function expirationTimeToken($token)
    // {
    // }
    // private function regeneratetoken()
    // {
    // }
    public function getToken()
    {
        return $this->session->get('test');
    }
}
