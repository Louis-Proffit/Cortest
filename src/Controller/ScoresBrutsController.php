<?php

namespace App\Controller;


use App\Core\Exception\DifferentSessionException;
use App\Core\Exception\NoReponsesCandidatException;
use App\Core\ReponseCandidat\CheckSingleSession;
use App\Core\ReponseCandidat\ReponsesCandidatSessionStorage;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Core\ScoreBrut\CorrecteurManager;
use App\Entity\Correcteur;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Form\CorrecteurChoiceType;
use App\Form\Data\CorrecteurChoice;
use App\Repository\SessionRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/score-brut", name: "calcul_score_brut_")]
class ScoresBrutsController extends AbstractController
{

    #[Route('/form/session/{session_id}', name: "session_form")]
    public function formSession(
        SessionRepository                      $sessionRepository,
        ReponsesCandidatSessionStorage         $reponsesCandidatSessionStorage,
        #[MapEntity(id: "session_id")] Session $session): Response
    {
        /** @var Session $session */
        $session = $sessionRepository->find($session);

        $reponsesCandidatsIds = array_map(fn(ReponseCandidat $reponseCandidat) => $reponseCandidat->id, $session->reponses_candidats->toArray());
        $reponsesCandidatSessionStorage->set($reponsesCandidatsIds);

        return $this->redirectToRoute("calcul_score_brut_form");
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route('/form', name: "form")]
    public function form(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        CheckSingleSession      $checkSingleSession,
        Request                 $request): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $parametresCalculScore = new CorrecteurChoice();
        $form = $this->createForm(CorrecteurChoiceType::class,
            $parametresCalculScore,
            [CorrecteurChoiceType::OPTION_SESSION => $session]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametresCalculScore->correcteur;

            return $this->redirectToRoute("calcul_score_brut_index", ["correcteur_id" => $correcteur->id]);
        }

        return $this->render('score_brut/form.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    #[Route("/index/{correcteur_id}", name: "index")]
    public function consulter(
        ReponsesCandidatStorage                      $reponsesCandidatStorage,
        CheckSingleSession                           $checkSingleSession,
        CorrecteurManager                            $correcteurManager,
        #[MapEntity(id: "correcteur_id")] Correcteur $correcteur
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();

        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $scores = $correcteurManager->corriger($correcteur, $reponsesCandidats);

        return $this->render("score_brut/index.html.twig", ["scores" => $scores, "session" => $session, "reponses_candidats" => $reponsesCandidats, "correcteur" => $correcteur]);
    }
}