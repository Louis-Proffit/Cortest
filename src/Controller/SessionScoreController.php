<?php

namespace App\Controller;


use App\Core\Res\Correcteur\CorrecteurManager;
use App\Core\Res\Grille\GrilleRepository;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\Session;
use App\Form\Data\CorrecteurChoice;
use App\Form\CorrecteurChoiceType;
use App\Repository\CorrecteurRepository;
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
        Request           $request,
        int               $session_id): Response
    {
        /** @var Session $session */
        $session = $session_repository->find($session_id);

        $parametres_calcul_score = new CorrecteurChoice();
        $form = $this->createForm(CorrecteurChoiceType::class,
            $parametres_calcul_score,
            [CorrecteurChoiceType::GRILLE_CLASS_OPTION => $session->grilleClass]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_score->correcteur;

            return $this->redirectToRoute("score_consulter",
                ["session_id" => $session_id, "correcteur_id" => $correcteur->id]);
        }

        return $this->render('scores/score_form.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/consulter/{session_id}/{correcteur_id}", name: "consulter")]
    public function consulter(
        SessionRepository $session_repository,
        CorrecteurRepository $correcteur_repository,
        CorrecteurManager $correcteur_manager,
        int $session_id,
        int $correcteur_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger($correcteur, $reponses);

        return $this->render("scores/cahier_des_charges.html.twig",
            ["scores" => $scores,
                "reponses" => $session->reponses_candidats->toArray(),
                "session" => $session,
                "correcteur" => $correcteur]);
    }


}