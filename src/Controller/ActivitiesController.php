<?php

namespace App\Controller;

use DateTime;
use App\Entity\Activity;
use App\Form\CreateActivityType;
use App\Repository\ActivityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActivitiesController extends AbstractController
{
    private $manager;
    private $activityRepo;

    public function __construct(
        ManagerRegistry $manager,
        ActivityRepository $activityRepo,
    ) {    
        $this->manager = $manager;
        $this->activityRepo = $activityRepo;
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
            $activity->setCreatedBy($this->getUser());
            $this->manager->getManager()->persist($activity);
            $this->manager->getManager()->flush();

            $this->addFlash("success", "La catégorie à bien été créé");
            return $this->redirectToRoute('dashboard');            
        }

        return $this->render('activities/addActivity.html.twig', [
            "form" => $form->createView()
        ]);
    }
    #[Route('/activity/edit/{id}', name: 'editActivity')]
    public function editActivity(Activity $activity, Request $request): Response
    {
        $form = $this->createForm(CreateActivityType::class, $activity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setCreatedBy($this->getUser());
            $this->manager->getManager()->persist($activity);
            $this->manager->getManager()->flush();

            $this->addFlash("success", "L'activité à bien été créé");
            return $this->redirectToRoute('dashboard');            
        }

        return $this->render('activities/editActivity.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/dailyAnalysis', name: 'dailyAnalysis')]
    public function dailyAnalysis(): Response
    {
        $user = $this->getUser();

        $todayStart = new DateTime('Europe/Paris');
        $todayStart->setTime(0, 0, 0);

        $tomorrowEnd = new DateTime('Europe/Paris');
        $tomorrowEnd->setTime(23, 59, 59);
        $tomorrowEnd->modify('+1 day');
        $activitiesOfTomorrow = $this->activityRepo->findAllDailyByUser($todayStart, $tomorrowEnd, $user);

        $totalSportTime = [];

        // Initialise le tableau totalSportTime pour chaque type d'activité à 0
        foreach ($user->getCategories() as $category) {
            $totalSportTime[$category->getName()] = 0;
        }

        // Calcule le temps total pour chaque type d'activité
        foreach ($activitiesOfTomorrow as $activity) {
            // Calcule la durée de l'activité
            $duration = $activity->getStartedAt()->diff($activity->getEndedAt());

            // Ajoute la durée à l'activité correspondante
            $totalSportTime[$activity->getCategory()->getName()] += $duration->h + ($duration->i / 60);
        }


        if ($totalSportTime['Sport'] < 3){
            $this->addFlash("danger", "Ajouter du sport");
        }
        if ($totalSportTime['Famille'] < 1){
            $this->addFlash("danger", "Ajouter de la famille");
        }
        if ($totalSportTime['Loisirs'] < 2){
            $this->addFlash("danger", "Ajouter du loisir");
        }
        if ($totalSportTime['Travail'] > 40){
            $this->addFlash("danger", "Moins de travail");
        }

       return $this->redirectToRoute('dashboard');
    }

    #[Route('/weeklyAnalysis', name: 'weeklyAnalysis')]
    public function weeklyAnalysis(): Response
    {
        $user = $this->getUser();

        $todayStart = new DateTime('Europe/Paris');
        $todayStart->setTime(0, 0, 0);

        $tomorrowEnd = new DateTime('Europe/Paris');
        $tomorrowEnd->setTime(23, 59, 59);
        $tomorrowEnd->modify('+5 day');
        $activitiesOfTomorrow = $this->activityRepo->findAllDailyByUser($todayStart, $tomorrowEnd, $user);

        $totalSportTime = [];

        // Initialise le tableau totalSportTime pour chaque type d'activité à 0
        foreach ($user->getCategories() as $category) {
            $totalSportTime[$category->getParent()] = 0;
        }

        // Calcule le temps total pour chaque type d'activité
        foreach ($activitiesOfTomorrow as $activity) {
            // Calcule la durée de l'activité
            $duration = $activity->getStartedAt()->diff($activity->getEndedAt());

            // Ajoute la durée à l'activité correspondante
            $totalSportTime[$activity->getCategory()->getParent()] += $duration->h + ($duration->i / 60);
        }


        if ($totalSportTime['Sport'] < 3){
            $this->addFlash("danger", "Ajouter du sport");
        }
        else if ($totalSportTime['Famille'] < 1){
            $this->addFlash("danger", "Ajouter de la famille");
        }
        else if ($totalSportTime['Loisirs'] < 2){
            $this->addFlash("danger", "Ajouter du loisir");
        }
        else if ($totalSportTime['Travail'] > 40){
            $this->addFlash("danger", "Moins de travail");
        }
        else{
            $this->addFlash("succes", "Votre planning est bon");
        }

       return $this->redirectToRoute('dashboard');
    }
}
