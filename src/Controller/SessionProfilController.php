<?php

namespace App\Controller;

use App\Core\Res\Correcteur\CorrecteurManager;
use App\Core\Res\Etalonnage\EtalonnageManager;
use App\Core\Res\Grille\GrilleRepository;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Form\Data\ParametresCalculProfil;
use App\Form\ParametresCalculProfilType;
use App\Repository\CorrecteurRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session-profil", name: "profil_")]
class SessionProfilController extends AbstractController
{

    #[Route('/{session_id}/{correcteur_id}', name: "form")]
    public function sessionProfilForm(
        CorrecteurManager       $correcteur_manager,
        EtalonnageManager       $etalonnage_manager,
        SessionRepository       $session_repository,
        GrilleRepository        $grille_repository,
        ProfilOuScoreRepository $profil_ou_score_repository,
        CorrecteurRepository    $correcteur_repository,
        Request                 $request,
        int                     $session_id,
        int                     $correcteur_id): Response
    {
        $session = $session_repository->find($session_id);

        $correcteur = $correcteur_repository->find($correcteur_id);

        if ($session->grille_id != $correcteur->grille_id) {
            throw new HttpException(Response::HTTP_BAD_REQUEST,
                "Le calculateur de score ne s'applique pas à la grille de la session considérée",);
        }

        $parametres_calcul_profil = new ParametresCalculProfil();
        $form = $this->createForm(
            ParametresCalculProfilType::class,
            $parametres_calcul_profil,
            [ParametresCalculProfilType::SCORE_ID_OPTION => $correcteur->score_id]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etalonnage = $parametres_calcul_profil->etalonnage;
            $grille = $grille_repository->get($correcteur->grille_id);
            $profil_ou_score = $profil_ou_score_repository->get($correcteur->score_id);

            $reponses = $session->reponses_candidats->toArray();

            $scores = $correcteur_manager->corriger(
                grille: $grille,
                score: $profil_ou_score,
                correcteur: $correcteur,
                session: $session
            );

            $profils = $etalonnage_manager->etalonner(
                etalonnage: $etalonnage,
                profil_ou_score: $profil_ou_score,
                scores: $scores
            );

            return $this->render("profils/cahier_des_charges.html.twig",
                ["profils" => $profils,
                    "reponses" => $reponses,
                    "scores" => $scores,
                    "session" => $session,
                    "etalonnage" => $etalonnage]);
        }

        return $this->render('profils/profil_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}