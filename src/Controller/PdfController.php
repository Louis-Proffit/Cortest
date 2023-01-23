<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Pdf\PdfManager;
use App\Form\Data\GraphiqueChoice;
use App\Form\GraphiqueChoiceType;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\GraphiqueRepository;
use App\Repository\ReponseCandidatRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pdf", name: "pdf_")]
class PdfController extends AbstractController
{

    #[Route("/session/form/{session_id}/{correcteur_id}/{etalonnage_id}", name: "session_form")]
    public function downloadZip(
        CorrecteurRepository $correcteur_repository,
        GraphiqueRepository  $graphique_repository,
        Request              $request,
        int                  $session_id,
        int                  $correcteur_id,
        int                  $etalonnage_id
    ): Response
    {
        $correcteur = $correcteur_repository->find($correcteur_id);

        $graphiques = $graphique_repository->findAll();

        if (empty($graphiques)) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphique_choice = new GraphiqueChoice(graphique: $graphiques[0]);
        $form = $this->createForm(GraphiqueChoiceType::class, $graphique_choice, [
            GraphiqueChoiceType::OPTION_PROFIL => $correcteur->profil
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            return $this->redirectToRoute("pdf_session_download", [
                "session_id" => $session_id,
                "etalonnage_id" => $etalonnage_id,
                "correcteur_id" => $correcteur_id,
                "graphique_id" => $graphique_choice->graphique->id
            ]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);

    }

    #[Route("/session/form/{session_id}/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "session_download")]
    public function form_session(
        SessionRepository    $session_repository,
        EtalonnageRepository $etalonnage_repository,
        CorrecteurRepository $correcteur_repository,
        GraphiqueRepository  $graphique_repository,
        CorrecteurManager    $correcteur_manager,
        EtalonnageManager    $etalonnage_manager,
        PdfManager           $pdf_manager,
        int                  $session_id,
        int                  $correcteur_id,
        int                  $etalonnage_id,
        int                  $graphique_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);

        $graphique = $graphique_repository->find($graphique_id);

        $scores = $correcteur_manager->corriger($correcteur, $session->reponses_candidats->toArray());
        $profils = $etalonnage_manager->etalonner($etalonnage, $scores);

        return $pdf_manager->createZipFile(
            session: $session,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scores: $scores,
            profils: $profils,
            graphique: $graphique,
        );
    }

    #[Route("/form/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}", name: "form")]
    public function form(
        GraphiqueRepository  $graphique_repository,
        CorrecteurRepository $correcteur_repository,
        Request              $request,
        int                  $candidat_reponse_id,
        int                  $correcteur_id,
        int                  $etalonnage_id
    ): Response
    {
        $correcteur = $correcteur_repository->find($correcteur_id);

        $graphiques = $graphique_repository->findAll();

        if (empty($graphiques)) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphique_choice = new GraphiqueChoice(
            graphique: $graphiques[0]
        );

        $form = $this->createForm(GraphiqueChoiceType::class, $graphique_choice, [
            GraphiqueChoiceType::OPTION_PROFIL => $correcteur->profil
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            return $this->redirectToRoute("pdf_download", [
                "candidat_reponse_id" => $candidat_reponse_id,
                "etalonnage_id" => $etalonnage_id,
                "graphique_id" => $graphique_choice->graphique,
                "correcteur_id" => $correcteur_id]);

        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);
    }

    #[Route("/download/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "download")]
    public function download(
        GraphiqueRepository       $graphique_repository,
        ReponseCandidatRepository $candidat_reponse_repository,
        CorrecteurRepository      $correcteur_repository,
        EtalonnageRepository      $etalonnage_repository,
        CorrecteurManager         $correcteur_manager,
        EtalonnageManager         $etalonnage_manager,
        PdfManager                $pdf_manager,
        int                       $candidat_reponse_id,
        int                       $correcteur_id,
        int                       $etalonnage_id,
        int                       $graphique_id
    ): Response
    {
        $correcteur = $correcteur_repository->find($correcteur_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);
        $candidat_reponse = $candidat_reponse_repository->find($candidat_reponse_id);

        $graphique = $graphique_repository->find($graphique_id);

        $scores = $correcteur_manager->corriger($correcteur, [$candidat_reponse]);
        $profils = $etalonnage_manager->etalonner($etalonnage, $scores);


        return $pdf_manager->createPdfFile(
            graphique: $graphique,
            candidat_reponse: $candidat_reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            score: $scores[$candidat_reponse_id],
            profil: $profils[$candidat_reponse_id]
        );
    }
}