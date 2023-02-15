<?php

namespace App\Controller;


use App\Core\Correcteur\CorrecteurManager;
use App\Core\Files\Csv\CsvManager;
use App\Core\Files\Csv\CsvScoreManager;
use App\Entity\Session;
use App\Form\CorrecteurChoiceType;
use App\Form\Data\CorrecteurChoice;
use App\Repository\CorrecteurRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/calcul/score", name: "calcul_score_")]
class SessionScoreController extends AbstractController
{

    #[Route('/form/{session_id}', name: "form")]
    public function form(
        SessionRepository $session_repository,
        Request           $request,
        int               $session_id): Response
    {
        /** @var Session $session */
        $session = $session_repository->find($session_id);

        $parametres_calcul_score = new CorrecteurChoice();
        $form = $this->createForm(CorrecteurChoiceType::class,
            $parametres_calcul_score,
            [CorrecteurChoiceType::OPTION_SESSION => $session]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_score->correcteur;

            return $this->redirectToRoute("calcul_score_index",
                ["session_id" => $session_id, "correcteur_id" => $correcteur->id]);
        }

        return $this->render('score/form.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/index/{session_id}/{correcteur_id}", name: "index")]
    public function consulter(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        CorrecteurManager    $correcteur_manager,
        int                  $session_id,
        int                  $correcteur_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger($correcteur, $reponses);

        return $this->render("score/index.html.twig",
            ["scores" => $scores,
                "session" => $session,
                "correcteur" => $correcteur]);
    }

    #[Route("/csv/{session_id}/{correcteur_id}", name: "csv")]
    public function csv(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        CorrecteurManager    $correcteur_manager,
        CsvScoreManager           $csv_score_manager,
        int                  $session_id,
        int                  $correcteur_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger($correcteur, $reponses);

        return $csv_score_manager->export($session, $correcteur->profil, $scores);
    }
}