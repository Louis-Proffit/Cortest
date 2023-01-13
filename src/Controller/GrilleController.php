<?php

namespace App\Controller;

use App\Core\Res\Grille\GrilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/grille", name: "grille_")]
class GrilleController extends AbstractController
{

    #[Route("/consulter", name: "consulter")]
    public function consulter(
        GrilleRepository $grille_repository
    ): Response
    {
        $grilles = $grille_repository->instanceOfAll();

        return $this->render("grille/index.html.twig", ["grilles" => $grilles]);
    }

}