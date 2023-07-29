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
use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\Session;
use App\Form\TestCorrecteurEtalonnageChoiceType;
use App\Form\Data\TestCorrecteurEtalonnageChoice;
use App\Form\Data\EtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/profil", name: "calcul_profil_")]
class SessionScoresEtalonnesController extends AbstractController
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

        $parametresCalculProfil = new TestCorrecteurEtalonnageChoice();

        $form = $this->createForm(
            TestCorrecteurEtalonnageChoiceType::class,
            $parametresCalculProfil,
            [TestCorrecteurEtalonnageChoiceType::OPTION_TEST => $session->test]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametresCalculProfil->value->correcteur;
            $etalonnage = $parametresCalculProfil->value->etalonnage;

            return $this->redirectToRoute("calcul_profil_index", ["correcteur_id" => $correcteur->id, "etalonnage_id" => $etalonnage->id]);
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/form/score/session/{session_id}/{correcteur_id}", name: "form_score_session")]
    public function formScoreSession(
        ReponsesCandidatStorage                $reponsesCandidatStorage,
        #[MapEntity(id: "session_id")] Session $session,
        int                                    $correcteur_id
    ): Response
    {
        $reponsesCandidatStorage->setFromSession($session);

        return $this->redirectToRoute("calcul_profil_form_score", ["correcteur_id" => $correcteur_id]);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route('/form/score/{correcteur_id}', name: "form_score")]
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
            $this->addFlash("danger", "Pas d'étalonnage disponible pour le profil " . $correcteur->structure->nom);
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
                "calcul_profil_index",
                [
                    "correcteur_id" => $correcteur->id,
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
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteur_manager,
        EtalonnageManager                            $etalonnage_manager,
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

        $scores = $correcteur_manager->corriger(
            correcteur: $correcteur,
            reponseCandidats: $reponsesCandidats
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