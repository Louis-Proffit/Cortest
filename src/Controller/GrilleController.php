<?php

namespace App\Controller;

use App\Repository\GrilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/grille", name: "grille_")]
class GrilleController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(GrilleRepository $grilleRepository): Response
    {
        $grilles = $grilleRepository->all();

        return $this->render("grille/index.html.twig", ["grilles" => $grilles]);
    }

}