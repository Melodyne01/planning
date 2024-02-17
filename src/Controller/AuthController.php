<?php

namespace App\Controller;

use Google\Client;
use Google\Service\Calendar\Calendar;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;


class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function index(): Response
    {
        $client = new Client();
        $client->setAuthConfig('json/credentials.json');
        $client->addScope("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/calendar.events");
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . ':8000/oauth/callback');
        $client->setAccessType('offline');        // offline access
        $client->setState("ItWorks!");
        $client->setIncludeGrantedScopes(true);   // incremental auth
        $client->setPrompt('select-account');

        $authUrl = "";

        $tokenPath = "";

    }

    #[Route('oauth/callback', name: 'callback')]
    public function callback(String $code, String $error, String $state): Response

    {
        
    }

}
