<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Entity\CortestLogEntry;
use App\Entity\ReponseCandidat;
use App\Form\ReponseCandidatType;
use App\Repository\ReponseCandidatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/reponse-candidat", name: "reponse_candidat_")]
class ReponseCandidatController extends AbstractController
{

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger         $activiteLogger,
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        ReponseCandidat        $reponseCandidat): Response
    {
        $sessionId = $reponseCandidat->session->id;

        $logger->info("Suppression des réponses du candidat . " . $reponseCandidat->id . " de la session " . $sessionId);

        $activiteLogger->persistAction(
            action: CortestLogEntry::ACTION_SUPPRIMER,
            object: $reponseCandidat,
            message: "Suppression d'une réponse de candidat"
        );
        $entityManager->remove($reponseCandidat);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du candidat enregistrée.");

        return $this->redirectToRoute("session_consulter", ["id" => $sessionId]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ActiviteLogger            $activiteLogger,
        LoggerInterface           $logger,
        Request                   $request,
        ReponseCandidatRepository $reponseCandidatRepository,
        EntityManagerInterface    $entityManager,
        ReponseCandidat           $reponseCandidat): Response
    {
        $sessionId = $reponseCandidat->session->id;

        $form = $this->createForm(ReponseCandidatType::class, $reponseCandidat);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $reponseCandidat->trimNames();
            $entityManager->persist($reponseCandidat);
            $this->addFlashIfCandidatAlreadyExists(
                reponseCandidatRepository: $reponseCandidatRepository,
                reponseCandidat: $reponseCandidat
            );
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_MODIFIER,
                object: $reponseCandidat,
                message: "Modification des réponses du candidat " . $reponseCandidat->prenom . " " . $reponseCandidat->nom
            );

            $entityManager->flush();

            $logger->info("Modification des réponses du candidat . " . $reponseCandidat->id . " de la session " . $sessionId);
            return $this->redirectToRoute("session_consulter", ["id" => $reponseCandidat->session->id]);
        }

        return $this->render("lecture/from_form.html.twig", ["form" => $form->createView()]);
    }

    public function addFlashIfCandidatAlreadyExists(
        ReponseCandidatRepository $reponseCandidatRepository,
        ReponseCandidat           $reponseCandidat): void
    {
        if ($reponseCandidatRepository->count(["nom" => $reponseCandidat->nom, "prenom" => $reponseCandidat->prenom, "session" => $reponseCandidat->session]) > 0) {
            $this->addFlash("warning", "Un candidat avec le nom " . $reponseCandidat->nom . " et le prénom " . $reponseCandidat->prenom . " existe déjà pour la session.");
        }
    }

}