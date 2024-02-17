<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $userRepo;
    private $jwt;
    private $manager;
    private $passwordHasher;

    public function __construct(
        ManagerRegistry $manager,
        UserRepository $userRepo,
        UserPasswordHasherInterface $passwordHasher,
    ) {    
        $this->manager = $manager;
        $this->userRepo = $userRepo;
        $this->passwordHasher = $passwordHasher;
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


        return $this->render('/user/index.html.twig', [
            'user' => $user,
        ]);
    }

}
