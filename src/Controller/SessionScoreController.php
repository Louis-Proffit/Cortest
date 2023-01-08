<?php

namespace App\Controller;

use App\Core\Computer\GrilleComputer;
use App\Core\Computer\ScoreComputer;
use App\Core\Entities\GrilleReponse;
use App\Entity\CandidatReponse;
use App\Entity\Session;
use App\Form\Data\ParametresCalculScore;
use App\Form\ParametresCalculScoreType;
use App\Repository\RuntimeResourcesRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session-score", name: "score_")]
class SessionScoreController extends AbstractController
{

    #[Route('/form/{session_id}', name: "form")]
    public function sessionScoreForm(
        SessionRepository $session_repository,
        GrilleComputer    $grille_computer,
        ScoreComputer     $score_computer,
        Request           $request,
        int               $session_id): Response
    {
        /** @var Session $session */
        $session = $session_repository->find($session_id);

        $parametres_calcul_score = new ParametresCalculScore();
        $form = $this->createForm(ParametresCalculScoreType::class,
            $parametres_calcul_score,
            [ParametresCalculScoreType::DEFINITION_GRILLE_OPTION => $session->grille]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $definition_score_computer = $parametres_calcul_score->definition_score_computer;

            $reponses = $grille_computer->computeAll($session->candidats->toArray(), $session->grille);
            $scores = $score_computer->computeAll($reponses, $definition_score_computer);

            return $this->render("scores/cahier_des_charges.html.twig",
                ["scores" => $scores,
                    "reponses" => $reponses,
                    "session" => $session,
                    "score_computer" => $definition_score_computer]);

        }

        return $this->render('scores/score_form.twig', [
            'form' => $form->createView(),
        ]);
    }
}