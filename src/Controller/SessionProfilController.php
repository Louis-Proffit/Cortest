<?php

namespace App\Controller;

use App\Core\Pdf\PdfManager;
use App\Core\Res\Correcteur\CorrecteurManager;
use App\Core\Res\Etalonnage\EtalonnageManager;
use App\Core\Res\ProfilGraphique\ProfilGraphiqueRepository;
use App\Form\CorrecteurEtEtalonnageChoiceType;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\Data\EtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use App\Repository\CandidatReponseRepository;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        CorrecteurManager    $correcteur_manager,
        EtalonnageManager    $etalonnage_manager,
        SessionRepository    $session_repository,
        EtalonnageRepository $etalonnage_repository,
        CorrecteurRepository $correcteur_repository,
        int                  $session_id,
        int                  $correcteur_id,
        int                  $etalonnage_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger(
            correcteur: $correcteur,
            reponses_candidats: $session->reponses_candidats->toArray()
        );

        $profils = $etalonnage_manager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        return $this->render("profils/cahier_des_charges.html.twig",
            ["profils" => $profils,
                "reponses" => $reponses,
                "scores" => $scores,
                "session" => $session,
                "correcteur" => $correcteur,
                "etalonnage" => $etalonnage]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route("/session/download/{session_id}/{correcteur_id}/{etalonnage_id}", name: "session_download")]
    public function downloadZip(
        SessionRepository         $session_repository,
        EtalonnageRepository      $etalonnage_repository,
        CorrecteurRepository      $correcteur_repository,
        CorrecteurManager         $correcteur_manager,
        EtalonnageManager         $etalonnage_manager,
        ProfilGraphiqueRepository $profil_graphique_manager,
        PdfManager                $pdf_manager,
        Request                   $request,
        int                       $session_id,
        int                       $correcteur_id,
        int                       $etalonnage_id
    ): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);

        $form = $this->createFormBuilder()
            ->add("profil_graphique", ChoiceType::class, [
                "choices" => $profil_graphique_manager->nomToProfilGraphique()
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil_graphique = $form->getData()["profil_graphique"];

            $scores = $correcteur_manager->corriger($correcteur, $session->reponses_candidats->toArray());
            $profils = $etalonnage_manager->etalonner($etalonnage, $scores);

            return $pdf_manager->createZipFile(
                session: $session,
                correcteur: $correcteur,
                etalonnage: $etalonnage,
                scores: $scores,
                profils: $profils,
                profil_graphique: $profil_graphique,
            );

        }
        return $this->render("profil_graphique/form.html.twig",
            ["form" => $form, "correcteur" => $correcteur, "etalonnage" => $etalonnage, "session" => $session]);

    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route("/download/{candidat_reponse_id}/{correcteur_id}/{etalonnage_id}", name: "download")]
    public function downloadFile(
        ProfilGraphiqueRepository $profil_graphique_manager,
        CandidatReponseRepository $candidat_reponse_repository,
        CorrecteurRepository      $correcteur_repository,
        EtalonnageRepository      $etalonnage_repository,
        CorrecteurManager         $correcteur_manager,
        EtalonnageManager         $etalonnage_manager,
        PdfManager                $pdf_manager,
        Request                   $request,
        int                       $candidat_reponse_id,
        int                       $correcteur_id,
        int                       $etalonnage_id
    ): Response
    {
        $correcteur = $correcteur_repository->find($correcteur_id);
        $etalonnage = $etalonnage_repository->find($etalonnage_id);
        $candidat_reponse = $candidat_reponse_repository->find($candidat_reponse_id);

        $form = $this->createFormBuilder()
            ->add("profil_graphique", ChoiceType::class, [
                "choices" => $profil_graphique_manager->nomToProfilGraphique()
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil_graphique = $form->getData()["profil_graphique"];


            $scores = $correcteur_manager->corriger($correcteur, [$candidat_reponse]);
            $profils = $etalonnage_manager->etalonner($etalonnage, $scores);


            return $pdf_manager->createPdfFile(
                profil_graphique: $profil_graphique,
                candidat_reponse: $candidat_reponse,
                correcteur: $correcteur,
                etalonnage: $etalonnage,
                score: $scores[$candidat_reponse_id],
                profil: $profils[$candidat_reponse_id]
            );

        }
        return $this->render("profil_graphique/form.html.twig",
            ["form" => $form, "correcteur" => $correcteur, "etalonnage" => $etalonnage, "session" => $candidat_reponse->session]);
    }
}