<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\CorrecteurEtalonnageMatcher;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Reponses\CheckSingleSession;
use App\Core\Reponses\DifferentSessionException;
use App\Core\Reponses\NoReponsesCandidatException;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Core\SessionCorrecteurMatcher;
use App\Form\CorrecteurEtEtalonnageChoiceType;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\Data\EtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\SessionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/profil", name: "calcul_profil_")]
class SessionProfilController extends AbstractController
{


    /**
     * Présélectionne toutes les réponses d'une session et redirige vers le formulaire correspondant
     * @param SessionRepository $sessionRepository
     * @param ReponsesCandidatStorage $reponsesCandidatStorage
     * @param int $session_id
     * @return Response
     */
    #[Route("/form/session/{session_id}", name: "form_session")]
    public function formSession(
        SessionRepository       $sessionRepository,
        ReponsesCandidatStorage $reponsesCandidatStorage,
        int                     $session_id,
    ): Response
    {
        $session = $sessionRepository->find($session_id);

        $reponsesCandidatStorage->setFromSession($session);

        return $this->redirectToRoute("calcul_profil_form");
    }

    /**
     * Formulaire pour choisir à la fois un correcteur et un étalonnage pour calculer un profil, à partir des réponses mises en cache.
     * @throws NoReponsesCandidatException
     * @throws DifferentSessionException
     */
    #[Route("/form", name: "form")]
    public function form(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        CheckSingleSession      $checkSingleSession,
        Request                 $request,
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $parametresCalculProfil = new CorrecteurEtEtalonnageChoice();

        $form = $this->createForm(
            CorrecteurEtEtalonnageChoiceType::class,
            $parametresCalculProfil,
            [CorrecteurEtEtalonnageChoiceType::OPTION_SESSION => $session]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametresCalculProfil->both->correcteur;
            $etalonnage = $parametresCalculProfil->both->etalonnage;

            return $this->redirectToRoute("calcul_profil_index", ["correcteur_id" => $correcteur->id, "etalonnage_id" => $etalonnage->id]);
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/form/score/session/{session_id}/{correcteur_id}", name: "form_score_session")]
    public function formScoreSession(
        SessionRepository       $sessionRepository,
        ReponsesCandidatStorage $reponsesCandidatStorage,
        LoggerInterface         $logger,
        int                     $session_id,
        int                     $correcteur_id,
    ): Response
    {
        $session = $sessionRepository->find($session_id);

        if ($session != null) {
            $reponsesCandidatStorage->setFromSession($session);

            return $this->redirectToRoute("calcul_profil_form_score", ["correcteur_id" => $correcteur_id]);
        } else {
            $this->addFlash("danger", "La session n'existe pas, ou a été supprimée");

            $logger->error("Ajout des réponses à partir d'une session qui n'existe pas : " . $session_id);

            return $this->redirectToRoute("home");
        }
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route('/form/score/{correcteur_id}', name: "form_score")]
    public function sessionProfilForm(
        ReponsesCandidatStorage  $reponsesCandidatStorage,
        CheckSingleSession       $checkSingleSession,
        SessionCorrecteurMatcher $sessionCorrecteurMatcher,
        CorrecteurRepository     $correcteurRepository,
        Request                  $request,
        int                      $correcteur_id): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $correcteur = $correcteurRepository->find($correcteur_id);

        if (!$sessionCorrecteurMatcher->match($session, $correcteur)) {
            $this->addFlash("danger", "La session et le correcteur sont incompatibles");
            return $this->redirectToRoute("home");
        }

        if ($correcteur->profil->etalonnages->isEmpty()) {
            $this->addFlash("danger", "Pas d'étalonnage disponible pour le profil " . $correcteur->profil->nom);
            return $this->redirectToRoute("etalonnage_index");
        }

        $parametres_calcul_profil = new EtalonnageChoice(etalonnage: $correcteur->profil->etalonnages[0]);

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
                    "correcteur_id" => $correcteur_id,
                    "etalonnage_id" => $etalonnage->id,
                ]
            );
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/index/{correcteur_id}/{etalonnage_id}", name: "index")]
    public function index(
        ReponsesCandidatStorage     $reponsesCandidatStorage,
        CheckSingleSession          $checkSingleSession,
        CorrecteurManager           $correcteur_manager,
        EtalonnageManager           $etalonnage_manager,
        EtalonnageRepository        $etalonnageRepository,
        CorrecteurRepository        $correcteurRepository,
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

        $scores = $correcteur_manager->corriger(
            correcteur: $correcteur,
            reponses_candidat: $reponsesCandidats
        );

        $profils = $etalonnage_manager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        return $this->render("profil/index_calcul.html.twig",
            ["profils" => $profils,
                "reponses_candidats" => $reponsesCandidats,
                "scores" => $scores,
                "session" => $session,
                "correcteur" => $correcteur,
                "etalonnage" => $etalonnage]);
    }
}