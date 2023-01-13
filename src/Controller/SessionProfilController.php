<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Pdf\PdfManager;
use App\Core\ProfilGraphique\ProfilGraphiqueRepository;
use App\Form\CorrecteurEtEtalonnageChoiceType;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\Data\EtalonnageChoice;
use App\Form\EtalonnageChoiceType;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\ReponseCandidatRepository;
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

#[Route("/calcul/profil", name: "calcul_profil_")]
class SessionProfilController extends AbstractController
{

    #[Route("/session/form/{session_id}", name: "session_form")]
    public function form(
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
            [CorrecteurEtEtalonnageChoiceType::OPTION_SESSION => $session]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $parametres_calcul_profil->both->correcteur;
            $etalonnage = $parametres_calcul_profil->both->etalonnage;

            return $this->redirectToRoute(
                "calcul_profil_index",
                [
                    "session_id" => $session_id,
                    "correcteur_id" => $correcteur->id,
                    "etalonnage_id" => $etalonnage->id
                ]
            );
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/score/form/{session_id}/{correcteur_id}', name: "score_form")]
    public function sessionProfilForm(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        Request              $request,
        int                  $session_id,
        int                  $correcteur_id): Response
    {
        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        if ($session->grille_class != $correcteur->grille_class) {
            throw new HttpException(Response::HTTP_BAD_REQUEST,
                "Le calculateur de score ne s'applique pas à la grille de la session considérée",);
        }

        $parametres_calcul_profil = new EtalonnageChoice();
        $form = $this->createForm(
            EtalonnageChoiceType::class,
            $parametres_calcul_profil,
            [EtalonnageChoiceType::OPTION_PROFIL => $correcteur->profil]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etalonnage = $parametres_calcul_profil->etalonnage;

            return $this->redirectToRoute(
                "calcul_profil_index",
                [
                    "session_id" => $session_id,
                    "correcteur_id" => $correcteur_id,
                    "etalonnage_id" => $etalonnage->id
                ]
            );
        }

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/index/{session_id}/{correcteur_id}/{etalonnage_id}", name: "index")]
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

        if ($session->grille_class !== $correcteur->grille_class) {
            throw new HttpException(Response::HTTP_BAD_REQUEST,
                "Le calculateur de score ne s'applique pas à la grille de la session considérée",);
        }

        if ($correcteur->profil->id !== $etalonnage->profil->id) {
            throw new HttpException(Response::HTTP_BAD_REQUEST,
                "L'étalonnage ne s'applique pas au profil calculé",);
        }

        $reponses = $session->reponses_candidats->toArray();

        $scores = $correcteur_manager->corriger(
            correcteur: $correcteur,
            reponses_candidat: $reponses
        );

        $profils = $etalonnage_manager->etalonner(
            etalonnage: $etalonnage,
            scores: $scores
        );

        return $this->render("profil/index.html.twig",
            ["profils" => $profils,
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
        ReponseCandidatRepository $candidat_reponse_repository,
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