<?php

namespace App\Controller;

use App\Core\Pdf\PdfManager;
use App\Core\Res\Correcteur\CorrecteurManager;
use App\Core\Res\Etalonnage\EtalonnageManager;
use App\Core\Res\Grille\GrilleRepository;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Form\CorrecteurEtEtalonnageChoiceType;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\Data\EtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route("/session-profil", name: "profil_")]
class SessionProfilController extends AbstractController
{

    #[Route("/form-from-reponse/{session_id}", name: "form_from_reponse")]
    public function sessionProfilFormFromReponse(
        SessionRepository $session_repository,
        Request           $request,
        int               $session_id,
    ): Response
    {
        $session = $session_repository->find($session_id);

        $parametres_calcul_profil = new CorrecteurEtEtalonnageChoice();

        $form = $this->createForm(
            CorrecteurEtEtalonnageChoiceType::class,
            $parametres_calcul_profil,
            [CorrecteurEtEtalonnageChoiceType::GRILLE_ID_OPTION => $session->grille_id]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_profil->correcteur_et_etalonnage->correcteur;
            $etalonnage = $parametres_calcul_profil->correcteur_et_etalonnage->etalonnage;

            return $this->redirectToRoute(
                "profil_consulter",
                [
                    "session_id" => $session_id,
                    "correcteur_id" => $correcteur->id,
                    "etalonnage_id" => $etalonnage->id
                ]
            );
        }

        return $this->render('profils/profil_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/form-from-score/{session_id}/{correcteur_id}', name: "form")]
    public function sessionProfilForm(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        Request              $request,
        int                  $session_id,
        int                  $correcteur_id): Response
    {
        $session = $session_repository->find($session_id);

        $correcteur = $correcteur_repository->find($correcteur_id);

        if ($session->grille_id != $correcteur->grille_id) {
            throw new HttpException(Response::HTTP_BAD_REQUEST,
                "Le calculateur de score ne s'applique pas à la grille de la session considérée",);
        }

        $parametres_calcul_profil = new EtalonnageChoice();
        $form = $this->createForm(
            EtalonnageChoiceType::class,
            $parametres_calcul_profil,
            [EtalonnageChoiceType::SCORE_ID_OPTION => $correcteur->score_id]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etalonnage = $parametres_calcul_profil->etalonnage;

            return $this->redirectToRoute(
                "profil_consulter",
                [
                    "session_id" => $session_id,
                    "correcteur_id" => $correcteur_id,
                    "etalonnage_id" => $etalonnage->id
                ]
            );
        }

        return $this->render('profils/profil_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/consulter/{session_id}/{correcteur_id}/{etalonnage_id}", name: "consulter")]
    public function consulter(
        CorrecteurManager       $correcteur_manager,
        EtalonnageManager       $etalonnage_manager,
        SessionRepository       $session_repository,
        GrilleRepository        $grille_repository,
        EtalonnageRepository    $etalonnage_repository,
        ProfilOuScoreRepository $profil_ou_score_repository,
        CorrecteurRepository    $correcteur_repository,
        int                     $session_id,
        int                     $correcteur_id,
        int                     $etalonnage_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);
        $correcteur = $correcteur_repository->find($correcteur_id);
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

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route("/file/{session_id}", name: "download")]
    public function download(
        SessionRepository $session_repository,
        PdfManager        $pdf_manager,
        int               $session_id
    ): Response
    {
        return $pdf_manager->createZipFile(
            session: $session_repository->find($session_id),
            template: "feuilles_profil/test.tex.twig"
        );
    }
}