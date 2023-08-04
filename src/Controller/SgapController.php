<?php

namespace App\Controller;

use App\Entity\Sgap;
use App\Form\SgapType;
use App\Repository\SgapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/sgap", name: "sgap_")]
class SgapController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        SgapRepository $sgapRepository,
    ): Response
    {
        $sgaps = $sgapRepository->findBy(criteria: [], orderBy: ["indice" => "ASC"]);

        return $this->render("sgap/index.html.twig", ["sgaps" => $sgaps]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new Sgap(
            id: 0,
            indice: 0,
            nom: ""
        );

        $form = $this->createForm(SgapType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute("sgap_index");

        }

        return $this->render("sgap/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        Request                $request,
        Sgap                   $sgap
    ): Response
    {
        $form = $this->createForm(SgapType::class, $sgap);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("sgap_index");
        }

        return $this->render("sgap/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Sgap                   $sgap): RedirectResponse
    {
        $logger->info("Suppression du sgap : " . $sgap->nom);
        $entityManager->remove($sgap);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du SGAP enregistrée");

        return $this->redirectToRoute("sgap_index");
    }

    #[Route("/revert/{id}", name: "revert")]
    public function revert(Sgap $sgap, EntityManagerInterface $entityManager): RedirectResponse
    {
        $logEntryRepository = $entityManager->getRepository(LogEntry::class);
        $logs = $logEntryRepository->getLogEntries($sgap);
        var_dump($logs);
        return $this->redirectToRoute("sgap_index");
    }
}