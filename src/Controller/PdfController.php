<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Files\Pdf\LatexCompilationFailedException;
use App\Core\Files\Pdf\PdfManager;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Form\Data\GraphiqueChoice;
use App\Form\GraphiqueChoiceType;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\GraphiqueRepository;
use App\Repository\ReponseCandidatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pdf", name: "pdf_")]
class PdfController extends AbstractController
{

    #[Route("/form/single/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}", name: "form_single")]
    public function form(
        GraphiqueRepository  $graphique_repository,
        CorrecteurRepository $correcteur_repository,
        Request              $request,
        int                  $candidat_reponse_id,
        int                  $correcteur_id,
        int                  $etalonnage_id): Response
    {
        $correcteur = $correcteur_repository->find($correcteur_id);

        $graphiques = $graphique_repository->findAll();

        if ($correcteur->profil->graphiques->isEmpty()) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphique_choice = new GraphiqueChoice(graphique: $graphiques[0]);

        $form = $this->createForm(GraphiqueChoiceType::class, $graphique_choice, [
            GraphiqueChoiceType::OPTION_PROFIL => $correcteur->profil
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            return $this->redirectToRoute("pdf_single", [
                "candidat_reponse_id" => $candidat_reponse_id,
                "etalonnage_id" => $etalonnage_id,
                "graphique_id" => $graphique_choice->graphique->id,
                "correcteur_id" => $correcteur_id]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);
    }

    #[Route("/form/zip/{correcteur_id}/{etalonnage_id}", name: "form_zip")]
    public function downloadZip(
        CorrecteurRepository $correcteur_repository,
        GraphiqueRepository  $graphique_repository,
        Request              $request,
        int                  $correcteur_id,
        int                  $etalonnage_id): Response
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

            return $this->redirectToRoute("pdf_zip", [
                "etalonnage_id" => $etalonnage_id,
                "correcteur_id" => $correcteur_id,
                "graphique_id" => $graphique_choice->graphique->id,
            ]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);

    }

    #[Route("/form/merged/{correcteur_id}/{etalonnage_id}", name: "form_merged")]
    public function downloadPdf(
        CorrecteurRepository $correcteur_repository,
        GraphiqueRepository  $graphique_repository,
        Request              $request,
        int                  $correcteur_id,
        int                  $etalonnage_id): Response
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

            return $this->redirectToRoute("pdf_merged", [
                "etalonnage_id" => $etalonnage_id,
                "correcteur_id" => $correcteur_id,
                "graphique_id" => $graphique_choice->graphique->id,
            ]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);

    }

    /**
     * @throws LatexCompilationFailedException
     */
    #[Route("/download/single/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "single")]
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
            reponseCandidat: $candidat_reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            score: $scores[$candidat_reponse_id],
            profil: $profils[$candidat_reponse_id]
        );
    }

    /**
     * @throws LatexCompilationFailedException
     */
    #[Route("/download/zip/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "zip")]
    public function formSessionZip(
        EtalonnageRepository    $etalonnageRepository,
        CorrecteurRepository    $correcteurRepository,
        GraphiqueRepository     $graphiqueRepository,
        CorrecteurManager       $correcteurManager,
        EtalonnageManager       $etalonnageManager,
        PdfManager              $pdfManager,
        ReponsesCandidatStorage $reponsesCandidatStorage,
        int                     $correcteur_id,
        int                     $etalonnage_id,
        int                     $graphique_id,
    ): Response
    {
        $correcteur = $correcteurRepository->find($correcteur_id);
        $etalonnage = $etalonnageRepository->find($etalonnage_id);

        $graphique = $graphiqueRepository->find($graphique_id);

        // TODO get session (for file name) and check it is consistent
        $reponses_candidat = $reponsesCandidatStorage->get();

        $scores = $correcteurManager->corriger($correcteur, $reponses_candidat);
        $profils = $etalonnageManager->etalonner($etalonnage, $scores);

        return $pdfManager->createZipFile(
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scores: $scores,
            profils: $profils,
            graphique: $graphique,
            reponsesCandidat: $reponses_candidat
        );
    }

    /**
     * @throws LatexCompilationFailedException
     */
    #[Route("/download/merged/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "merged")]
    public function formSessionPdf(
        EtalonnageRepository    $etalonnageRepository,
        CorrecteurRepository    $correcteurRepository,
        GraphiqueRepository     $graphiqueRepository,
        CorrecteurManager       $correcteurManager,
        EtalonnageManager       $etalonnageManager,
        PdfManager              $pdfManager,
        ReponsesCandidatStorage $reponsesCandidatStorage,
        int                     $correcteur_id,
        int                     $etalonnage_id,
        int                     $graphique_id,
    ): Response
    {
        $correcteur = $correcteurRepository->find($correcteur_id);
        $etalonnage = $etalonnageRepository->find($etalonnage_id);

        $graphique = $graphiqueRepository->find($graphique_id);

        // TODO get session (for file name) and check it is consistent
        $reponses_candidat = $reponsesCandidatStorage->get();

        $scores = $correcteurManager->corriger($correcteur, $reponses_candidat);
        $profils = $etalonnageManager->etalonner($etalonnage, $scores);

        return $pdfManager->createPdfMergedFile(
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scores: $scores,
            profils: $profils,
            graphique: $graphique,
            reponsesCandidat: $reponses_candidat
        );
    }
}
