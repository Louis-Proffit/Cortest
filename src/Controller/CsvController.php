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
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Core\SessionCorrecteurMatcher;
use App\Recherche\ReponsesCandidatSessionStorage;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\ReponseCandidatRepository;
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
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();

        $data = $exportReponsesCandidat->export($reponsesCandidats);
        $fileName = "export_recherche_reponses.csv"; // TODO generify ?

        return $csvManager->export($data, $fileName);
    }

    #[Route("/scores/{correcteur_id}", name: "scores")]
    public function scores(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        CheckSingleSession      $checkSingleSession,
        CorrecteurRepository    $correcteur_repository,
        CorrecteurManager       $correcteur_manager,
        ExportScores            $csv_score_manager,
        FileNameManager         $fileNameManager,
        CsvManager              $csvManager,
        int                     $correcteur_id
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $correcteur = $correcteur_repository->find($correcteur_id);

        $scores = $correcteur_manager->corriger($correcteur, $reponsesCandidats);

        $data = $csv_score_manager->export(profil: $correcteur->profil, scores: $scores, reponses: $reponsesCandidats);

        $file_name = $fileNameManager->sessionScoreCsvFileName($session);

        return $csvManager->export($data, $file_name);
    }

    #[Route("/profils/{correcteur_id}/{etalonnage_id}", name: "profils")]
    public function profils(
        ReponsesCandidatStorage     $reponsesCandidatStorage,
        CheckSingleSession          $checkSingleSession,
        CorrecteurManager           $correcteurManager,
        EtalonnageManager           $etalonnageManager,
        EtalonnageRepository        $etalonnageRepository,
        CorrecteurRepository        $correcteurRepository,
        ExportProfils               $exportProfils,
        FileNameManager             $fileNameManager,
        CsvManager                  $csvManager,
        SessionCorrecteurMatcher    $sessionCorrecteurMatcher,
        CorrecteurEtalonnageMatcher $correcteurEtalonnageMatcher,
        int                         $correcteur_id,
        int                         $etalonnage_id,
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $correcteur = $correcteurRepository->find($correcteur_id);

        if (!$sessionCorrecteurMatcher->match($session, $correcteur)) {
            $this->addFlash("danger", "La session et le correcteur sont incompatibles");
            return $this->redirectToRoute("home");
        }

        $etalonnage = $etalonnageRepository->find($etalonnage_id);

        if (!$correcteurEtalonnageMatcher->match($correcteur, $etalonnage)) {
            $this->addFlash("danger", "Le correcteur et l'étalonnage choisis sont incompatibles");
            return $this->redirectToRoute("home");
        }

        $scores = $correcteurManager->corriger(
            correcteur: $correcteur,
            reponses_candidat: $reponsesCandidats
        );

        $profils = $etalonnageManager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        $data = $exportProfils->export(profil: $correcteur->profil, profils: $profils, reponses: $reponsesCandidats);

        $fileName = $fileNameManager->sessionProfilCsvFileName($session);
        return $csvManager->export($data, $fileName);
    }

}