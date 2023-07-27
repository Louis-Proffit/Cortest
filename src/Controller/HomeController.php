<?php

namespace App\Controller;

use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: "home")]
    public function index(
        ResourceRepository $resourceRepository
    ): Response
    {
        return $this->render('home.html.twig', ["resources" => $resourceRepository->findAll()]);
    }
}