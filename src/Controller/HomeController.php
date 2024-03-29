<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController

{
    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(RecipeRepository $repository): Response
    {
        // Todo
        return $this->render('pages/home.html.twig', [
            'recipes' => $repository->findPublicRecipe(3),
        ]);
    }
}
