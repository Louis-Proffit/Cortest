<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'hello'
        ]);
    }

    #[Route('/score/{id}', name: 'calculer_score')]
    public function scoreCalculator(Request $request, int $id): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'user'
        ]);
    }

    #[Route('/reponse/{id}', name: 'calculer_reponse')]
    public function reponseCalculator(Request $request, int $id): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'user'
        ]);
    }
}
