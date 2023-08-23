<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Core\CorrecteurEtalonnageMatcher;
use App\Core\Exception\DifferentSessionException;
use App\Core\Exception\NoReponsesCandidatException;
use App\Core\IO\CsvManager;
use App\Core\IO\FileNameManager;
use App\Core\ReponseCandidat\CheckSingleSession;
use App\Core\ReponseCandidat\ExportReponsesCandidat;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Core\ScoreBrut\CorrecteurManager;
use App\Core\ScoreBrut\ExportScoresBruts;
use App\Core\ScoreEtalonne\EtalonnageManager;
use App\Core\ScoreEtalonne\ExportScoresEtalonnes;
use App\Core\SessionCorrecteurMatcher;
use App\Entity\Correcteur;
use App\Entity\CortestLogEntry;
use App\Entity\Etalonnage;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/csv", name: "csv_")]
class CsvController extends AbstractController
{

    #[Route("/reponses", name: "reponses")]
    public function reponses(
        ActiviteLogger          $activiteLogger,
        ReponsesCandidatStorage $reponsesCandidatStorage,
        ExportReponsesCandidat  $exportReponsesCandidat,
        CsvManager              $csvManager,
        FileNameManager         $fileNameManager
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();

        $content = $exportReponsesCandidat->export($reponsesCandidats);

        $fileName = $fileNameManager->reponsesCsvFileName($reponsesCandidats);

        $activiteLogger->persist(
            action: CortestLogEntry::ACTION_EXPORTER,
            message: "Export de réponses de candidats",
            data: ["fichier" => $fileName, "nombre" => count($reponsesCandidats)]
        );
        $activiteLogger->flush();

        return $csvManager->export($content, $fileName);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/scores-bruts/{correcteur_id}", name: "scores_bruts")]
    public function scores(
        ActiviteLogger                               $activiteLogger,
        SessionCorrecteurMatcher                     $sessionCorrecteurMatcher,
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteurManager,
        ExportScoresBruts                            $exportScores,
        FileNameManager                              $fileNameManager,
        CsvManager                                   $csvManager,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();

        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        if (!$sessionCorrecteurMatcher->match($session, $correcteur)) {
            $this->addFlash("danger", "La session et le correcteur sont incompatibles (pas le même concours).");
            return $this->redirectToRoute("home");
        }

        $scoresBruts = $correcteurManager->corriger($correcteur, $reponsesCandidats);

        $data = $exportScores->export(structure: $correcteur->structure, scoresBruts: $scoresBruts, reponsesCandidat: $reponsesCandidats);

        $fileName = $fileNameManager->sessionScoreCsvFileName($session);

        $activiteLogger->persistExportCalcul(
            calcul: $scoresBruts,
            message: "Export de scores bruts de candidats",
            data: ["fichier" => $fileName]
        );
        $activiteLogger->flush();

        return $csvManager->export($data, $fileName);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/scores-etalonnes/{correcteur_id}/{etalonnage_id}", name: "scores_etalonnes")]
    public function profils(
        ActiviteLogger                               $activiteLogger,
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteurManager,
        EtalonnageManager                            $etalonnageManager,
        ExportScoresEtalonnes                        $exportProfils,
        FileNameManager                              $fileNameManager,
        CsvManager                                   $csvManager,
        SessionCorrecteurMatcher                     $sessionCorrecteurMatcher,
        CorrecteurEtalonnageMatcher                  $correcteurEtalonnageMatcher,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur,
        #[MapEntity(id: "etalonnage_id")] Etalonnage $etalonnage
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        if (!$sessionCorrecteurMatcher->match($session, $correcteur)) {
            $this->addFlash("danger", "La session et le correcteur sont incompatibles");
            return $this->redirectToRoute("home");
        }

        if (!$correcteurEtalonnageMatcher->match($correcteur, $etalonnage)) {
            $this->addFlash("danger", "Le correcteur et l'étalonnage choisis sont incompatibles");
            return $this->redirectToRoute("home");
        }

        $scoreBruts = $correcteurManager->corriger(
            correcteur: $correcteur,
            reponseCandidats: $reponsesCandidats
        );

        $scoresEtalonnes = $etalonnageManager->etalonner(
            etalonnage: $etalonnage,
            reponsesCandidat: $reponsesCandidats,
            scoresBruts: $scoreBruts
        );

        $data = $exportProfils->export(structure: $correcteur->structure, scoresEtalonnes: $scoresEtalonnes, reponses: $reponsesCandidats);

        $fileName = $fileNameManager->sessionProfilCsvFileName($session);

        $activiteLogger->persistExportCalcul(
            calcul: $scoresEtalonnes,
            message: "Export de scores étalonnés de candidats",
            data: ["fichier" => $fileName]
        );
        $activiteLogger->flush();

        return $csvManager->export($data, $fileName);
    }

}