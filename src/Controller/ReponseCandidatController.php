<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Entity\CortestLogEntry;
use App\Entity\ReponseCandidat;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
}