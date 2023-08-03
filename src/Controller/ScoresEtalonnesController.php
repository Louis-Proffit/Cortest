<?php

namespace App\Controller;

use App\Core\CorrecteurEtalonnageMatcher;
use App\Core\Exception\DifferentSessionException;
use App\Core\Exception\NoReponsesCandidatException;
use App\Core\ReponseCandidat\CheckSingleSession;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Core\ScoreBrut\CorrecteurManager;
use App\Core\ScoreEtalonne\EtalonnageManager;
use App\Core\SessionCorrecteurMatcher;
use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\Session;
use App\Form\Data\EtalonnageChoice;
use App\Form\Data\TestCorrecteurEtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use App\Form\TestCorrecteurEtalonnageChoiceType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/score-etalonne", name: "calcul_score_etalonne_")]
class ScoresEtalonnesController extends AbstractController
{

    /**
     * Présélectionne toutes les réponses d'une session et redirige vers le formulaire correspondant
     * @param ReponsesCandidatStorage $reponsesCandidatStorage
     * @param Session $session
     * @return Response
     */
    #[Route("/form/session/{session_id}", name: "form_session")]
    public function formSession(
        ReponsesCandidatStorage                $reponsesCandidatStorage,
        #[MapEntity(id: "session_id")] Session $session
    ): Response
    {
        $reponsesCandidatStorage->setFromSession($session);

        return $this->redirectToRoute("calcul_score_etalonne_form");
    }

    /**
     * Formulaire pour choisir à la fois un correcteur et un étalonnage pour calculer un score_etalonne, à partir des réponses mises en cache.
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

        $parametresCalculProfil = new TestCorrecteurEtalonnageChoice();

        $form = $this->createForm(
            TestCorrecteurEtalonnageChoiceType::class,
            $parametresCalculProfil,
            [TestCorrecteurEtalonnageChoiceType::OPTION_TEST => $session->test]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametresCalculProfil->value->correcteur;
            $etalonnage = $parametresCalculProfil->value->etalonnage;

            return $this->redirectToRoute("calcul_score_etalonne_index", ["correcteur_id" => $correcteur->id, "etalonnage_id" => $etalonnage->id]);
        }

        return $this->render('score_etalonne/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/form/score-brut/session/{session_id}/{correcteur_id}", name: "form_score_session")]
    public function formScoreSession(
        ReponsesCandidatStorage                $reponsesCandidatStorage,
        #[MapEntity(id: "session_id")] Session $session,
        int                                    $correcteur_id
    ): Response
    {
        $reponsesCandidatStorage->setFromSession($session);

        return $this->redirectToRoute("calcul_score_etalonne_form_score", ["correcteur_id" => $correcteur_id]);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route('/form/score-brut/{correcteur_id}', name: "form_score")]
    public function sessionProfilForm(
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        SessionCorrecteurMatcher                     $sessionCorrecteurMatcher,
        Request                                      $request,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        if (!$sessionCorrecteurMatcher->match($session, $correcteur)) {
            $this->addFlash("danger", "La session et le correcteur sont incompatibles");
            return $this->redirectToRoute("home");
        }

        if ($correcteur->structure->etalonnages->isEmpty()) {
            $this->addFlash("danger", "Pas d'étalonnage disponible pour le score_etalonne " . $correcteur->structure->nom);
            return $this->redirectToRoute("etalonnage_index");
        }

        $parametres_calcul_profil = new EtalonnageChoice(etalonnage: $correcteur->structure->etalonnages[0]);

        $form = $this->createForm(
            EtalonnageChoiceType::class,
            $parametres_calcul_profil,
            [EtalonnageChoiceType::OPTION_STRUCTURE => $correcteur->structure]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etalonnage = $parametres_calcul_profil->etalonnage;

            return $this->redirectToRoute(
                "calcul_score_etalonne_index",
                [
                    "correcteur_id" => $correcteur->id,
                    "etalonnage_id" => $etalonnage->id,
                ]
            );
        }

        return $this->render('score_etalonne/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/index/{correcteur_id}/{etalonnage_id}", name: "index")]
    public function index(
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteurManager,
        EtalonnageManager                            $etalonnageManager,
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

        $scoresBruts = $correcteurManager->corriger(
            correcteur: $correcteur,
            reponseCandidats: $reponsesCandidats
        );

        $scoresEtalonnes = $etalonnageManager->etalonner(
            etalonnage: $etalonnage,
            reponsesCandidat: $reponsesCandidats,
            scoresBruts: $scoresBruts
        );

        return $this->render("score_etalonne/index.html.twig",
            ["scores_etalonnes" => $scoresEtalonnes,
                "scores_bruts" => $scoresBruts,
                "reponses_candidats" => $reponsesCandidats,
                "session" => $session,
                "correcteur" => $correcteur,
                "etalonnage" => $etalonnage]);
    }
}