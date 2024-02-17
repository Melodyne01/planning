<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CreateCategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriesController extends AbstractController
{
    private $manager;

    public function __construct(
        ManagerRegistry $manager,
    ) {    
        $this->manager = $manager;
    }
    
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
        ]);
    }
    #[Route('/category/add', name: 'addCategory')]
    public function addCategory(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CreateCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->manager->getManager()->persist($category);
            $this->manager->getManager()->flush();

            $this->addFlash("success", "La catégorie à bien été créé");
            return $this->redirectToRoute('dashboard');            
        }

        return $this->render('categories/addCategory.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
