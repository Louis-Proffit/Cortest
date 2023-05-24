<?php

namespace App\Controller;

use App\Repository\ReponseCandidatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/reponse-candidat", name: "reponse_candidat_")]
class ReponseCandidatController extends AbstractController
{

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface    $entity_manager,
        ReponseCandidatRepository $reponse_candidat_repository,
        int                       $id): Response
    {
        $reponseCandidat = $reponse_candidat_repository->find($id);

        if ($reponseCandidat == null) {
            $this->addFlash("danger", "Les réponses de ce candidat n'existent pas ou ont été supprimées.");
            return $this->redirectToRoute("home");
        }

        $sessionId = $reponseCandidat->session->id;

        $entity_manager->remove($reponseCandidat);
        $entity_manager->flush();

        return $this->redirectToRoute("session_consulter", ["id" => $sessionId]);
    }
}