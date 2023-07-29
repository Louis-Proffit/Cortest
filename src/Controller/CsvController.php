<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\CorrecteurEtalonnageMatcher;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Files\CsvManager;
use App\Core\Files\FileNameManager;
use App\Core\IO\Profil\ExportProfils;
use App\Core\IO\ReponseCandidat\ExportReponsesCandidat;
use App\Core\IO\Score\ExportScores;
use App\Core\Reponses\CheckSingleSession;
use App\Core\Reponses\DifferentSessionException;
use App\Core\Reponses\NoReponsesCandidatException;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Core\SessionCorrecteurMatcher;
use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/csv", name: "csv_")]
class CsvController extends AbstractController
{

    #[Route("/reponses", name: "reponses")]
    public function reponses(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        ExportReponsesCandidat  $exportReponsesCandidat,
        CsvManager              $csvManager,
        FileNameManager         $fileNameManager
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();

        $data = $exportReponsesCandidat->export($reponsesCandidats);

        $fileName = $fileNameManager->reponsesCsvFileName($reponsesCandidats);

        return $csvManager->export($data, $fileName);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/scores/{correcteur_id}", name: "scores")]
    public function scores(
        SessionCorrecteurMatcher                     $sessionCorrecteurMatcher,
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteurManager,
        ExportScores                                 $exportScores,
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

        $scores = $correcteurManager->corriger($correcteur, $reponsesCandidats);

        $data = $exportScores->export(profil: $correcteur->structure, scores: $scores, reponses: $reponsesCandidats);

        $file_name = $fileNameManager->sessionScoreCsvFileName($session);

        return $csvManager->export($data, $file_name);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/profils/{correcteur_id}/{etalonnage_id}", name: "profils")]
    public function profils(
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteurManager,
        EtalonnageManager                            $etalonnageManager,
        ExportProfils                                $exportProfils,
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

        $scores = $correcteurManager->corriger(
            correcteur: $correcteur,
            reponseCandidats: $reponsesCandidats
        );

        $profils = $etalonnageManager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        $data = $exportProfils->export(profil: $correcteur->structure, profils: $profils, reponses: $reponsesCandidats);

        $fileName = $fileNameManager->sessionProfilCsvFileName($session);
        return $csvManager->export($data, $fileName);
    }

}