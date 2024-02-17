<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EventsController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public HttpClientInterface $client;
    public function index(): Response
    {
        
        $this->client = $client;
        $response = $this->client->request(
            'GET',
            'https://www.googleapis.com/calendar/v3/calendars/primary/events'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200, le contenu a été téléchargé correctement
        $content = $response->getContent();
        // $content contient le contenu brut de la réponse

        return new Response($content);
    }
}

        /*return $this->render('events/index.html.twig', [
            'controller_name' => 'EventsController',
        ]);*/
