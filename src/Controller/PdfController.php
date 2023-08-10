<?php

namespace App\Controller;

use App\Core\Exception\DifferentSessionException;
use App\Core\Exception\MissingFileException;
use App\Core\Exception\NoReponsesCandidatException;
use App\Core\IO\Pdf\Compiler\LatexCompilationFailedException;
use App\Core\IO\Pdf\PdfManager;
use App\Core\ReponseCandidat\CheckSingleSession;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Core\ScoreBrut\CorrecteurManager;
use App\Core\ScoreEtalonne\EtalonnageManager;
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
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

#[Route("/pdf", name: "pdf_")]
class PdfController extends AbstractController
{
    #[Route("/form/simple/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}", name: "form_single")]
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

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute("pdf_single", [
                "candidat_reponse_id" => $candidat_reponse_id,
                "etalonnage_id" => $etalonnage_id,
                "graphique_id" => $graphiqueChoice->graphique->id,
                "correcteur_id" => $correcteur->id]);
        }

        return $this->render("score_etalonne/form_graphique.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/form/zip/{correcteur_id}/{etalonnage_id}", name: "form_zip")]
    public function downloadZip(
        GraphiqueRepository                          $graphiqueRepository,
        Request                                      $request,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        int                                          $etalonnage_id): Response
    {
        $graphique = $graphiqueRepository->findOneBy([]);

        if ($graphique == null) {
            $this->addFlash("warning", "Pas de graphique disponible, veuillez en créer un");
            return $this->redirectToRoute("graphique_index");
        }

        $graphiqueChoice = new GraphiqueChoice(graphique: $graphique);
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

        return $this->render("score_etalonne/form_graphique.html.twig", ["form" => $form->createView()]);

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

        return $this->render("score_etalonne/form_graphique.html.twig", ["form" => $form->createView()]);

    }

    /**
     * @param CorrecteurManager $correcteurManager
     * @param EtalonnageManager $etalonnageManager
     * @param PdfManager $pdfManager
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param Graphique $graphique
     * @param ReponseCandidat $reponseCandidat
     * @return Response
     * @throws LatexCompilationFailedException
     * @throws LoaderError
     * @throws MissingFileException
     * @throws SyntaxError
     */
    #[Route("/telecharger/simple/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "single")]
    public function download(
        CorrecteurManager                                       $correcteurManager,
        EtalonnageManager                                       $etalonnageManager,
        PdfManager                                              $pdfManager,
        #[MapEntity(id: "candidat_reponse_id")] ReponseCandidat $reponseCandidat,
        #[MapEntity(id: "correcteur_id")] Correcteur            $correcteur,
        #[MapEntity(id: "etalonnage_id")] Etalonnage            $etalonnage,
        #[MapEntity(id: "graphique_id")] Graphique              $graphique,
    ): Response
    {
        $reponsesCandidat = [$reponseCandidat];
        $scores = $correcteurManager->corriger(correcteur: $correcteur, reponseCandidats: $reponsesCandidat);
        $profils = $etalonnageManager->etalonner(etalonnage: $etalonnage, reponsesCandidat: $reponsesCandidat, scoresBruts: $scores);

        return $pdfManager->createPdfFile(
            graphique: $graphique,
            reponseCandidat: $reponseCandidat,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scoreBrut: $scores->get($reponseCandidat),
            scoreEtalonne: $profils->get($reponseCandidat)
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
     * @throws LoaderError
     * @throws MissingFileException
     * @throws NoReponsesCandidatException
     * @throws SyntaxError
     */
    #[Route("/telecharger/zip/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "zip")]
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

        $scores = $correcteurManager->corriger(correcteur: $correcteur, reponseCandidats: $reponsesCandidats);
        $profils = $etalonnageManager->etalonner(etalonnage: $etalonnage, reponsesCandidat: $reponsesCandidats, scoresBruts: $scores);

        return $pdfManager->createZipFile(
            session: $session,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scoresBruts: $scores,
            scoresEtalonnes: $profils,
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
     * @throws MissingFileException
     * @throws LoaderError
     * @throws SyntaxError
     */
    #[Route("/telecharger/merged/{correcteur_id}/{etalonnage_id}/{graphique_id}", name: "merged")]
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

        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $scores = $correcteurManager->corriger(correcteur: $correcteur, reponseCandidats: $reponsesCandidats);
        $profils = $etalonnageManager->etalonner(etalonnage: $etalonnage, reponsesCandidat: $reponsesCandidats, scoresBruts: $scores);

        return $pdfManager->createPdfMergedFile(
            session: $session,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scoresBruts: $scores,
            scoresEtalonnes: $profils,
            graphique: $graphique,
            reponsesCandidat: $reponsesCandidats
        );
    }
}
