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
        $reponse_candidat = $reponse_candidat_repository->find($id);

        $session_id = $reponse_candidat->session->id;

        $entity_manager->remove($reponse_candidat);
        $entity_manager->flush();

        return $this->redirectToRoute("session_consulter", ["id" => $session_id]);
    }
}