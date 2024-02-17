<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CreateCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriesController extends AbstractController
{
    private $manager;
    private $categoryRepo;

    public function __construct(
        ManagerRegistry $manager,
        CategoryRepository $categoryRepo,
    ) {    
        $this->manager = $manager;
        $this->categoryRepo = $categoryRepo;
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
        $categories = $this->categoryRepo->findAllByUser($this->getUser());
        $category = new Category();

        $form = $this->createForm(CreateCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedBy($this->getUser());
            
            $this->manager->getManager()->persist($category);
            $this->manager->getManager()->flush();

            $this->addFlash("success", "La catégorie à bien été créé");
            return $this->redirectToRoute('addCategory');            
        }

        return $this->render('categories/addCategory.html.twig', [
            "form" => $form->createView(),
            "categories" => $categories,
        ]);
    }
}
