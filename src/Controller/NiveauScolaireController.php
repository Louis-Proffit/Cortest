<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Entity\CortestLogEntry;
use App\Entity\NiveauScolaire;
use App\Entity\ReponseCandidat;
use App\Form\NiveauScolaireType;
use App\Repository\NiveauScolaireRepository;
use App\Repository\ReponseCandidatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/niveau-scolaire", name: "niveau_scolaire_")]
class NiveauScolaireController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        NiveauScolaireRepository $niveauScolaireRepository
    ): Response
    {
        $items = $niveauScolaireRepository->findBy(criteria: [], orderBy: ["indice" => "ASC"]);

        return $this->render("niveau_scolaire/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        Request                $request
    ): RedirectResponse|Response
    {
        $niveauScolaire = new NiveauScolaire(id: 0, indice: 0, nom: "");

        $form = $this->createForm(NiveauScolaireType::class, $niveauScolaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($niveauScolaire);
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_CREER,
                object: $niveauScolaire,
                message: "Création d'un niveau scolaire par formulaire"
            );
            $entityManager->flush();

            return $this->redirectToRoute("niveau_scolaire_index");

        }

        return $this->render("niveau_scolaire/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        Request                $request,
        NiveauScolaire         $niveauScolaire
    ): Response
    {
        $form = $this->createForm(NiveauScolaireType::class, $niveauScolaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_MODIFIER,
                object: $niveauScolaire,
                message: "Modification d'un niveau scolaire par formulaire"
            );
            $entityManager->flush();

            return $this->redirectToRoute("niveau_scolaire_index");
        }

        return $this->render("niveau_scolaire/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/confirmer/{id}", name: "supprimer_confirmer")]
    public function supprimerConfirmer(
        NiveauScolaire $niveauScolaire
    ): Response
    {
        $supprimable = NiveauScolaire::supprimable($niveauScolaire);
        return $this->render("niveau_scolaire/supprimer.html.twig", [
            "niveau_scolaire" => $niveauScolaire,
            "supprimable" => $supprimable
        ]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger         $activiteLogger,
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        NiveauScolaire         $niveauScolaire): RedirectResponse
    {
        $supprimable = NiveauScolaire::supprimable($niveauScolaire);

        if ($supprimable) {
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_SUPPRIMER,
                object: $niveauScolaire,
                message: "Suppression d'un niveau scolaire"
            );
            $entityManager->remove($niveauScolaire);
            $entityManager->flush();

            $this->addFlash("success", "Suppression effectuée.");

            return $this->redirectToRoute("niveau_scolaire_index");
        } else {
            $logger->error("Supression d'un niveau scolaire non supprimable", ["niveau_scolaire" => $niveauScolaire]);
            $this->addFlash("danger", "Impossible de supprimer le niveau scolaire.");

            return $this->redirectToRoute("niveau_scolaire_supprimer_confirmer", ["id" => $niveauScolaire->id]);
        }
    }
}