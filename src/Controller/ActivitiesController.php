<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\CreateActivityType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActivitiesController extends AbstractController
{
    private $manager;

    public function __construct(
        ManagerRegistry $manager,
    ) {    
        $this->manager = $manager;
    }
    
    #[Route('/activities', name: 'app_activities')]
    public function index(): Response
    {
        return $this->render('activities/index.html.twig', [
            'controller_name' => 'ActivitiesController',
        ]);
    }
    #[Route('/activity/add', name: 'addActivity')]
    public function addActivity(Request $request): Response
    {
        $activity = new Activity();

        $form = $this->createForm(CreateActivityType::class, $activity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->manager->getManager()->persist($activity);
            $this->manager->getManager()->flush();

            $this->addFlash("success", "La catégorie à bien été créé");
            return $this->redirectToRoute('dashboard');            
        }

        return $this->render('activities/addActivity.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
