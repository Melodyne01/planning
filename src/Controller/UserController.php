<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $userRepo;
    private $manager;
    private $passwordHasher;
    private $activityRepo;

    public function __construct(
        ManagerRegistry $manager,
        UserRepository $userRepo,
        UserPasswordHasherInterface $passwordHasher,
        ActivityRepository $activityRepo
    ) {    
        $this->manager = $manager;
        $this->userRepo = $userRepo;
        $this->passwordHasher = $passwordHasher;
        $this->activityRepo = $activityRepo;
    }
    
    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('dashboard');
        }
        
        $this->CheckUserInDatabase();
        //Creation d'un nouvel objet User
        $user = new User();
        //Creation du formulaire sur base d'un formulaire crée au préalable 
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        //Vérification de la conformité des données entrées par l'utilisateur
        if ($form->isSubmitted() && $form->isValid()) {
            //Chiffrement du mot de passe selon l'algorytme Bcrypt
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $this->manager->getManager()->persist($user);
            //Envoie des données vers la base de données
            $this->manager->getManager()->flush();
            $this->addFlash("success", "Le compte à bien été créé");            
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'login')]
    public function index(): Response
    {
        $currentUser = $this->getUser();
        // Si l'user est déjà connecté
        if (!empty($currentUser)){
            return $this->redirectToRoute('dashboard');
        }
        $this->addFlash("danger", "Les informations de connexion ne sont pas valides");
        return $this->redirectToRoute('register');
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        
    }
    public function CheckUserInDatabase()
    {
        $userList = $this->userRepo->findAll();
        //S'il n'y a pas d'users
        if($userList == null){
            $user = new User();
            $user->setFirstname("Tanguy");
            $user->setLastname("Baldewyns");  
            $user->setEmail("tanguy.baldewyns@gmail.com");
            $hashedPassword = $this->passwordHasher->hashPassword($user,"aaaaaa");
            $user->setPassword($hashedPassword);
            $this->manager->getManager()->persist($user);
            //Envoie des données vers la base de données
            $this->manager->getManager()->flush(); 
        }
    }

    #[Route('/user/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();

        $yesterdayStart = new DateTime('Europe/Paris');
        $yesterdayStart->modify('-1 day');
        $yesterdayStart->setTime(0, 0, 0);

        $yesterdayEnd = new DateTime('Europe/Paris');
        $yesterdayEnd->modify('-1 day');
        $yesterdayEnd->setTime(23, 59, 59);
        $activitiesOfYesterday = $this->activityRepo->findAllDailyByUser($yesterdayStart, $yesterdayEnd, $user);

        $todayStart = new DateTime('Europe/Paris');
        $todayStart->setTime(0, 0, 0);

        $todayEnd = new DateTime('Europe/Paris');
        $todayEnd->setTime(23, 59, 59);
        $activitiesOfToday = $this->activityRepo->findAllDailyByUser($todayStart, $todayEnd, $user);

        $tomorrowStart = new DateTime('Europe/Paris');
        $tomorrowStart->setTime(0, 0, 0);
        $tomorrowStart->modify('+1 day');

        $tomorrowEnd = new DateTime('Europe/Paris');
        $tomorrowEnd->setTime(23, 59, 59);
        $tomorrowEnd->modify('+1 day');
        $activitiesOfTomorrow = $this->activityRepo->findAllDailyByUser($tomorrowStart, $tomorrowEnd, $user);

        // Jour +2
        $dayAfterTomorrowStart = new DateTime('Europe/Paris');
        $dayAfterTomorrowStart->setTime(0, 0, 0);
        $dayAfterTomorrowStart->modify('+2 days');

        $dayAfterTomorrowEnd = new DateTime('Europe/Paris');
        $dayAfterTomorrowEnd->setTime(23, 59, 59);
        $dayAfterTomorrowEnd->modify('+2 days');
        $activitiesOfTheDayAfterTomorrow = $this->activityRepo->findAllDailyByUser($dayAfterTomorrowStart, $dayAfterTomorrowEnd, $user);

        // Jour +3
        $twoDaysAfterTomorrowStart = new DateTime('Europe/Paris');
        $twoDaysAfterTomorrowStart->setTime(0, 0, 0);
        $twoDaysAfterTomorrowStart->modify('+3 days');

        $twoDaysAfterTomorrowEnd = new DateTime('Europe/Paris');
        $twoDaysAfterTomorrowEnd->setTime(23, 59, 59);
        $twoDaysAfterTomorrowEnd->modify('+3 days');
        $activitiesOfTwoDaysAfterTomorrow = $this->activityRepo->findAllDailyByUser($twoDaysAfterTomorrowStart, $twoDaysAfterTomorrowEnd, $user);

        
        // Jour +4
        $threeDaysAfterTomorrowStart = new DateTime('Europe/Paris');
        $threeDaysAfterTomorrowStart->setTime(0, 0, 0);
        $threeDaysAfterTomorrowStart->modify('+4 days');

        $threeDaysAfterTomorrowEnd = new DateTime('Europe/Paris');
        $threeDaysAfterTomorrowEnd->setTime(23, 59, 59);
        $threeDaysAfterTomorrowEnd->modify('+4 days');
        $activitiesOfThreeDaysAfterTomorrow = $this->activityRepo->findAllDailyByUser($threeDaysAfterTomorrowStart, $threeDaysAfterTomorrowEnd, $user);

        return $this->render('/user/index.html.twig', [
            'user' => $user,
            'activitiesOfYesterday' => $activitiesOfYesterday,
            'activitiesOfToday' => $activitiesOfToday,
            'activitiesOfTomorrow' => $activitiesOfTomorrow,
            'activitiesOfTheDayAfterTomorrow' => $activitiesOfTheDayAfterTomorrow,
            'activitiesOfTwoDaysAfterTomorrow' => $activitiesOfTwoDaysAfterTomorrow,
            'activitiesOfThreeDaysAfterTomorrow' => $activitiesOfThreeDaysAfterTomorrow,
            'yesterdayStart' => $yesterdayStart,
            'todayStart' => $todayStart,
            'tomorrowStart' => $tomorrowStart,
            'dayAfterTomorrowStart' => $dayAfterTomorrowStart,
            'twoDaysAfterTomorrowStart' => $twoDaysAfterTomorrowStart,
            'threeDaysAfterTomorrowStart' => $threeDaysAfterTomorrowStart,
        ]);
    }

}
