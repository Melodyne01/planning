<?php

namespace App\Controller;

use DateTime;
use App\Entity\Activity;
use App\Form\CreateActivityType;
use App\Form\EditActivityType;
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
        $form = $this->createForm(EditActivityType::class, $activity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setCreatedBy($this->getUser());
            $this->manager->getManager()->persist($activity);
            $this->manager->getManager()->flush();

            $this->addFlash("success", "L'activité à bien été modifiée");
            return $this->redirectToRoute('dashboard');            
        }
        return $this->render('activities/edit.html.twig', [
            "form" => $form->createView(),
            "activity" => $activity
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

        // Création d'une liste de plages horaires disponibles pour la journée
        $plagesHorairesDisponibles = [
            ['start' => new DateTime('Europe/Paris 00:00:00'), 'end' => new DateTime('Europe/Paris 23:59:59')]
        ];

        // Parcours de toutes les activités existantes et marquage des plages horaires correspondantes comme non disponibles
        foreach ($activitiesOfTomorrow as $activity) {
            $start = $activity->getStartedAt();
            $end = $activity->getEndedAt();

            // Suppression des plages horaires qui chevauchent l'activité
            for ($i = 0; $i < count($plagesHorairesDisponibles); $i++) {
                $plage = $plagesHorairesDisponibles[$i];

                // Vérification du chevauchement
                if ($start < $plage['end'] && $end > $plage['start']) {
                    // Divise la plage horaire en deux si elle chevauche l'activité
                    if ($start > $plage['start']) {
                        // La partie avant l'activité
                        $plagesHorairesDisponibles[$i]['end'] = $start;
                    }
                    if ($end < $plage['end']) {
                        // La partie après l'activité
                        array_splice($plagesHorairesDisponibles, $i + 1, 0, [['start' => $end, 'end' => $plage['end']]]);
                        $plagesHorairesDisponibles[$i]['end'] = $end;
                    }
                }
            }
        }
        if ($totalSportTime['Sport'] < 3){
            if($plagesHorairesDisponibles[0]){
                $this->addFlash("danger", "Votre agenda semble manquer de sport : Le 1er horaire disponible est le ".$plagesHorairesDisponibles[0]['start']->format('d M à H:i'));
            }else{
                $this->addFlash("danger", "Ajouter du sport");
            }
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
