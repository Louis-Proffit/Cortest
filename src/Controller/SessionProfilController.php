<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Files\Csv\CsvProfilManager;
use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\Session;
use App\Form\CorrecteurEtEtalonnageChoiceType;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\Data\EtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/profil", name: "calcul_profil_")]
class SessionProfilController extends AbstractController
{

    #[Route("/session/form/{session_id}", name: "session_form")]
    public function form(
        SessionRepository $session_repository,
        Request           $request,
        int               $session_id,
    ): Response
    {
        $session = $session_repository->find($session_id);

        $parametres_calcul_profil = new CorrecteurEtEtalonnageChoice();

        $form = $this->createForm(
            CorrecteurEtEtalonnageChoiceType::class,
            $parametres_calcul_profil,
            [CorrecteurEtEtalonnageChoiceType::OPTION_SESSION => $session]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_profil->both->correcteur;
            $etalonnage = $parametres_calcul_profil->both->etalonnage;

            return $this->redirectToRoute(
                "calcul_profil_index",
                [
                    "session_id" => $session_id,
                    "correcteur_id" => $correcteur->id,
                    "etalonnage_id" => $etalonnage->id
                ]
            );
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/score/form/{session_id}/{correcteur_id}', name: "score_form")]
    public function sessionProfilForm(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        EtalonnageRepository $etalonnage_repository,
        Request              $request,
        int                  $session_id,
        int                  $correcteur_id): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        if (($response = $this->declineInvalidConcours($session, $correcteur)) != null) {
            return $response;
        }

        $parametres_calcul_profil = new EtalonnageChoice(etalonnage: $etalonnage_repository->findOneBy([]));
        $form = $this->createForm(
            EtalonnageChoiceType::class,
            $parametres_calcul_profil,
            [EtalonnageChoiceType::OPTION_PROFIL => $correcteur->profil]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etalonnage = $parametres_calcul_profil->etalonnage;

            return $this->redirectToRoute(
                "calcul_profil_index",
                [
                    "session_id" => $session_id,
                    "correcteur_id" => $correcteur_id,
                    "etalonnage_id" => $etalonnage->id
                ]
            );
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/index/{session_id}/{correcteur_id}/{etalonnage_id}", name: "index")]
    public function consulter(
        CorrecteurManager    $correcteur_manager,
        EtalonnageManager    $etalonnage_manager,
        SessionRepository    $session_repository,
        EtalonnageRepository $etalonnage_repository,
        CorrecteurRepository $correcteur_repository,
        int                  $session_id,
        int                  $correcteur_id,
        int                  $etalonnage_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        if (($response = $this->declineInvalidConcours($session, $correcteur)) != null) {
            return $response;
        }

        if (($response = $this->declineInvalidProfil($correcteur, $etalonnage)) != null) {
            return $response;
        }

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger(
            correcteur: $correcteur,
            reponses_candidat: $reponses
        );

        $profils = $etalonnage_manager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        return $this->render("profil/index_calcul.html.twig",
            ["profils" => $profils,
                "scores" => $scores,
                "session" => $session,
                "correcteur" => $correcteur,
                "etalonnage" => $etalonnage]);
    }

    #[Route("/csv/{session_id}/{correcteur_id}/{etalonnage_id}", name: "csv")]
    public function csv(
        CorrecteurManager    $correcteur_manager,
        EtalonnageManager    $etalonnage_manager,
        SessionRepository    $session_repository,
        EtalonnageRepository $etalonnage_repository,
        CorrecteurRepository $correcteur_repository,
        CsvProfilManager     $csv_profil_manager,
        int                  $session_id,
        int                  $correcteur_id,
        int                  $etalonnage_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        if (($response = $this->declineInvalidConcours($session, $correcteur)) != null) {
            return $response;
        }

        if (($response = $this->declineInvalidProfil($correcteur, $etalonnage)) != null) {
            return $response;
        }

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger(
            correcteur: $correcteur,
            reponses_candidat: $reponses
        );

        $profils = $etalonnage_manager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        return $csv_profil_manager->export(
            session: $session,
            profil: $correcteur->profil,
            profils: $profils
        );
    }


    private function declineInvalidProfil(Correcteur $correcteur, Etalonnage $etalonnage): ?Response
    {
        if ($correcteur->profil->id !== $etalonnage->profil->id) {
            $this->addFlash("warning",
                "Le correcteur et l'étalonnage sélectionnés ne correspondent pas au même profil.");
            return new Response("");
        }

        return null;
    }

    private function declineInvalidConcours(Session $session, Correcteur $correcteur): ?Response
    {
        if ($session->concours->id !== $correcteur->concours->id) {
            $this->addFlash("warning", "La session correspond à un concours que le correcteur ne support pas.");
            return new Response("");
        }
        return null;
    }
}