<?php

namespace App\Controller;

use App\Entity\Echelle;
use App\Form\EchelleType;
use App\Repository\EchelleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/echelle", name: "echelle_")]
class EchelleController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        EchelleRepository $echelleRepository
    ): Response
    {
        return $this->render("echelle/index.html.twig", ["items" => $echelleRepository->findAll()]);
    }
}