<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Files\Pdf\Compiler\LatexCompilationFailedException;
use App\Core\Files\Pdf\PdfManager;
use App\Core\Reponses\CheckSingleSession;
use App\Core\Reponses\DifferentSessionException;
use App\Core\Reponses\NoReponsesCandidatException;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\ReponseCandidat;
use App\Form\Data\GraphiqueChoice;
use App\Form\GraphiqueChoiceType;
use App\Repository\GraphiqueRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pdf", name: "pdf_")]
class PdfController extends AbstractController
{
    #[Route("/form/single/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}", name: "form_single")]
    public function form(
        GraphiqueRepository                          $graphiqueRepository,
        Request                                      $request,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        int                                          $candidat_reponse_id,
        int                                          $etalonnage_id): Response
    {
        $graphiques = $graphiqueRepository->findAll();

        if ($correcteur->structure->graphiques->isEmpty()) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphiqueChoice = new GraphiqueChoice(graphique: $graphiques[0]);

        $form = $this->createForm(GraphiqueChoiceType::class, $graphiqueChoice, [
            GraphiqueChoiceType::OPTION_PROFIL => $correcteur->structure
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            return $this->redirectToRoute("pdf_single", [
                "candidat_reponse_id" => $candidat_reponse_id,
                "etalonnage_id" => $etalonnage_id,
                "graphique_id" => $graphiqueChoice->graphique->id,
                "correcteur_id" => $correcteur->id]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);
    }

    #[Route("/form/zip/{correcteur_id}/{etalonnage_id}", name: "form_zip")]
    public function downloadZip(
        GraphiqueRepository                          $graphiqueRepository,
        Request                                      $request,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        int                                          $etalonnage_id): Response
    {
        $graphiques = $graphiqueRepository->findAll();

        if (empty($graphiques)) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphiqueChoice = new GraphiqueChoice(graphique: $graphiques[0]);
        $form = $this->createForm(GraphiqueChoiceType::class, $graphiqueChoice, [
            GraphiqueChoiceType::OPTION_PROFIL => $correcteur->structure
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            return $this->redirectToRoute("pdf_zip", [
                "etalonnage_id" => $etalonnage_id,
                "correcteur_id" => $correcteur->id,
                "graphique_id" => $graphiqueChoice->graphique->id,
            ]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);

    }

    #[Route("/form/merged/{correcteur_id}/{etalonnage_id}", name: "form_merged")]
    public function downloadPdf(
        GraphiqueRepository                          $graphiqueRepository,
        Request                                      $request,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        int                                          $etalonnage_id): Response
    {
        $graphiques = $graphiqueRepository->findAll();

        if (empty($graphiques)) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphiqueChoice = new GraphiqueChoice(graphique: $graphiques[0]);

        $form = $this->createForm(GraphiqueChoiceType::class, $graphiqueChoice, [
            GraphiqueChoiceType::OPTION_PROFIL => $correcteur->structure
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            return $this->redirectToRoute("pdf_merged", [
                "etalonnage_id" => $etalonnage_id,
                "correcteur_id" => $correcteur->id,
                "graphique_id" => $graphiqueChoice->graphique->id,
            ]);
        }

        return $this->render("profil/form_graphique.html.twig", ["form" => $form]);

    }

    /**
     * @throws LatexCompilationFailedException
     */
    #[Route("/download/single/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "single")]
    public function download(
        CorrecteurManager                                       $correcteurManager,
        EtalonnageManager                                       $etalonnageManager,
        PdfManager                                              $pdfManager,
        #[MapEntity(id: "correcteur_id")] Correcteur            $correcteur,
        #[MapEntity(id: "etalonnage_id")] Etalonnage            $etalonnage,
        #[MapEntity(id: "graphique_id")] Graphique              $graphique,
        #[MapEntity(id: "candidat_reponse_id")] ReponseCandidat $reponseCandidat,
    ): Response
    {
        $scores = $correcteurManager->corriger($correcteur, [$reponseCandidat]);
        $profils = $etalonnageManager->etalonner($etalonnage, $scores);

        return $pdfManager->createPdfFile(
            graphique: $graphique,
            reponseCandidat: $reponseCandidat,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            score: $scores[$reponseCandidat->id],
            profil: $profils[$reponseCandidat->id]
        );
    }

    /**
     * @param CorrecteurManager $correcteurManager
     * @param EtalonnageManager $etalonnageManager
     * @param PdfManager $pdfManager
     * @param ReponsesCandidatStorage $reponsesCandidatStorage
     * @param CheckSingleSession $checkSingleSession
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param Graphique $graphique
     * @return Response
     * @throws DifferentSessionException
     * @throws LatexCompilationFailedException
     * @throws NoReponsesCandidatException
     */
    #[Route("/download/zip/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "zip")]
    public function formSessionZip(
        CorrecteurManager                            $correcteurManager,
        EtalonnageManager                            $etalonnageManager,
        PdfManager                                   $pdfManager,
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        #[MapEntity(id: "etalonnage_id")] Etalonnage $etalonnage,
        #[MapEntity(id: "graphique_id")] Graphique   $graphique,
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $scores = $correcteurManager->corriger($correcteur, $reponsesCandidats);
        $profils = $etalonnageManager->etalonner($etalonnage, $scores);

        return $pdfManager->createZipFile(
            session: $session,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scores: $scores,
            profils: $profils,
            graphique: $graphique,
            reponsesCandidat: $reponsesCandidats
        );
    }

    /**
     * @param CorrecteurManager $correcteurManager
     * @param EtalonnageManager $etalonnageManager
     * @param PdfManager $pdfManager
     * @param ReponsesCandidatStorage $reponsesCandidatStorage
     * @param CheckSingleSession $checkSingleSession
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param Graphique $graphique
     * @return Response
     * @throws DifferentSessionException
     * @throws LatexCompilationFailedException
     * @throws NoReponsesCandidatException
     */
    #[Route("/download/merged/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "merged")]
    public function formSessionPdf(
        CorrecteurManager                            $correcteurManager,
        EtalonnageManager                            $etalonnageManager,
        PdfManager                                   $pdfManager,
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        #[MapEntity(id: "etalonnage_id")] Etalonnage $etalonnage,
        #[MapEntity(id: "graphique_id")] Graphique   $graphique,
    ): Response
    {

        $reponses_candidat = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponses_candidat);

        $scores = $correcteurManager->corriger($correcteur, $reponses_candidat);
        $profils = $etalonnageManager->etalonner($etalonnage, $scores);

        return $pdfManager->createPdfMergedFile(
            session: $session,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scores: $scores,
            profils: $profils,
            graphique: $graphique,
            reponsesCandidat: $reponses_candidat
        );
    }
}
