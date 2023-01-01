<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\UploadSessionBase;
use App\Form\UploadSessionBaseType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/home', name: "home")]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'hello'
        ]);
    }

    #[Route("/calcul/base", name: 'calcul_base')]
    public function calculerBase(ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): Response
    {
        $sessions = $doctrine->getRepository(Session::class)->findAll();

        $logger->info("Sessions à choisir : " . count($sessions));

        $uploadSessionBase = new UploadSessionBase();
        $form = $this->createForm(UploadSessionBaseType::class, $uploadSessionBase, ["sessions" => $sessions]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $logger->debug($uploadSessionBase->contents);

            return $this->redirectToRoute('home');
        }

        return $this->render('scanner/scanner_csv_base.html.twig', [
            'form' => $form->createView(),
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
