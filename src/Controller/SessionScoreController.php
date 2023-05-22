<?php

namespace App\Controller;


use App\Core\Correcteur\CorrecteurManager;
use App\Core\Reponses\CheckSingleSession;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Form\CorrecteurChoiceType;
use App\Form\Data\CorrecteurChoice;
use App\Recherche\ReponsesCandidatSessionStorage;
use App\Repository\CorrecteurRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/score", name: "calcul_score_")]
class SessionScoreController extends AbstractController
{

    #[Route('/form/session/{session_id}', name: "session_form")]
    public function formSession(
        SessionRepository              $session_repository,
        ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage,
        int                            $session_id): Response
    {
        /** @var Session $session */
        $session = $session_repository->find($session_id);

        $reponsesCandidatsIds = array_map(fn(ReponseCandidat $reponseCandidat) => $reponseCandidat->id, $session->reponses_candidats->toArray());
        $reponsesCandidatSessionStorage->set($reponsesCandidatsIds);

        return $this->redirectToRoute("calcul_score_form");
    }

    #[Route('/form', name: "form")]
    public function form(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        CheckSingleSession      $checkSingleSession,
        Request                 $request): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $parametres_calcul_score = new CorrecteurChoice();
        $form = $this->createForm(CorrecteurChoiceType::class,
            $parametres_calcul_score,
            [CorrecteurChoiceType::OPTION_SESSION => $session]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_score->correcteur;

            return $this->redirectToRoute("calcul_score_index", ["correcteur_id" => $correcteur->id]);
        }

        return $this->render('score/form.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/index/{correcteur_id}", name: "index")]
    public function consulter(
        ReponsesCandidatStorage        $reponsesCandidatStorage,
        CheckSingleSession             $checkSingleSession,
        CorrecteurRepository           $correcteur_repository,
        CorrecteurManager              $correcteur_manager,
        int                            $correcteur_id
    ): Response
    {
        $reponsesCandidats = $reponsesCandidatStorage->get();
        $session = $checkSingleSession->findCommonSession($reponsesCandidats);

        $correcteur = $correcteur_repository->find($correcteur_id);

        $scores = $correcteur_manager->corriger($correcteur, $reponsesCandidats);

        return $this->render("score/index.html.twig", ["scores" => $scores, "session" => $session, "reponses_candidats" => $reponsesCandidats, "correcteur" => $correcteur]);
    }
}