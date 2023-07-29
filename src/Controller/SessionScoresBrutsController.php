<?php

namespace App\Controller;


use App\Core\Correcteur\CorrecteurManager;
use App\Core\Reponses\CheckSingleSession;
use App\Core\Reponses\DifferentSessionException;
use App\Core\Reponses\NoReponsesCandidatException;
use App\Core\Reponses\ReponsesCandidatSessionStorage;
use App\Core\Reponses\ReponsesCandidatStorage;
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

#[Route("/calcul/score", name: "calcul_score_")]
class SessionScoresBrutsController extends AbstractController
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

        return $this->redirectToRoute("calcul_score_form");
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

            return $this->redirectToRoute("calcul_score_index", ["correcteur_id" => $correcteur->id]);
        }

        return $this->render('score/form.twig', [
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

        return $this->render("score/index.html.twig", ["scores" => $scores, "session" => $session, "reponses_candidats" => $reponsesCandidats, "correcteur" => $correcteur]);
    }
}