<?php

namespace App\Controller;


use App\Core\Res\Correcteur\CorrecteurManager;
use App\Core\Res\Grille\GrilleRepository;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\Session;
use App\Form\Data\ParametresCalculScore;
use App\Form\ParametresCalculScoreType;
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
        GrilleRepository $grille_repository,
        ProfilOuScoreRepository $profil_ou_score_repository,
        SessionRepository $session_repository,
        CorrecteurManager $correcteur_manager,
        Request $request,
        int $session_id): Response
    {
        /** @var Session $session */
        $session = $session_repository->find($session_id);

        $parametres_calcul_score = new ParametresCalculScore();
        $form = $this->createForm(ParametresCalculScoreType::class,
            $parametres_calcul_score,
            [ParametresCalculScoreType::GRILLE_ID_OPTION => $session->grille_id]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_score->correcteur;
            $grille = $grille_repository->get($correcteur->grille_id);
            $score = $profil_ou_score_repository->get($correcteur->score_id);

            $scores = $correcteur_manager->corriger($grille, $score, $correcteur, $session);

            return $this->render("scores/cahier_des_charges.html.twig",
                ["scores" => $scores,
                    "reponses" => $session->reponses_candidats->toArray(),
                    "session" => $session,
                    "correcteur" => $correcteur]);

        }

        return $this->render('scores/score_form.twig', [
            'form' => $form->createView(),
        ]);
    }
}