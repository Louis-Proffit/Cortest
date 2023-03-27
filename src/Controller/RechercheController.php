<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Files\Csv\CsvReponseManager;
use App\Core\Files\Csv\CsvScoreManager;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Form\CorrecteurChoiceType;
use App\Form\Data\CorrecteurChoice;
use App\Form\Data\RechercheFiltre;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\Data\RechercheReponsesCandidat;
use App\Form\RechercheFiltreType;
use App\Form\RechercheReponsesCandidatType;
use App\Recherche\FiltreSessionStorage;
use App\Recherche\ReponsesCandidatSessionStorage;
use App\Repository\CorrecteurRepository;
use App\Repository\ReponseCandidatRepository;
use App\Repository\SessionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/recherche", name: "recherche_")]
class RechercheController extends AbstractController
{

    #[Route("/download/reponses", name: "download_reponses")]
    public function downloadReponses(
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage,
        ReponseCandidatRepository      $reponse_candidat_repository,
        CsvReponseManager              $csv_reponse_manager,
    ): BinaryFileResponse
    {
        $cached_reponses_ids = $reponses_candidat_session_storage->get();
        $cached_reponses = $reponse_candidat_repository->findAllByIds($cached_reponses_ids);
        return $csv_reponse_manager->export($cached_reponses, "export_recherche_reponses.csv");
    }

    #[Route("/vider", name: "vider")]
    public function vider(
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage
    ): Response
    {
        $reponses_candidat_session_storage->set(array());
        $this->addFlash("success", "Les candidats ont été retirés, vous pouvez en sélectionner de nouveaux.");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/calculer/scores", name: "calculer_scores")]
    public function calculerScores(
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage,
        ReponseCandidatRepository      $reponse_candidat_repository,
    ): Response
    {
        $cached_reponses_ids = $reponses_candidat_session_storage->get();
        $cached_reponses = $reponse_candidat_repository->findAllByIds($cached_reponses_ids);

        if (count($cached_reponses) == 0) {
            $this->addFlash("warning", "Il faut sélectionner au moins un candidat");
            return $this->redirectToRoute("recherche_index");
        }

        $session_id = $cached_reponses[0]->session->id;
        foreach ($cached_reponses as $reponse){
            if ($session_id != $reponse->session->id){
                $this->addFlash("warning", "Pour calculer les scores les candidats doivent appartenir à la même session");
                return $this->redirectToRoute("recherche_index");
            }
        }

        return $this->redirectToRoute("recherche_form_correcteur", ['session_id' => $session_id]);
    }
    #[Route('/form/correcteur/{session_id}', name: "form_correcteur")]
    public function formCorrecteur(
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

            return $this->redirectToRoute("recherche_score",
                ["session_id" => $session_id, "correcteur_id" => $correcteur->id]);
        }

        return $this->render('score/form.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route("/score/{session_id}/{correcteur_id}", name: "score")]
    public function consulter_score(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        CorrecteurManager    $correcteur_manager,
        int                  $session_id,
        int                  $correcteur_id,
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage,
        ReponseCandidatRepository      $reponse_candidat_repository,
    ): Response
    {
        $cached_reponses_ids = $reponses_candidat_session_storage->get();
        $reponses = $reponse_candidat_repository->findAllByIds($cached_reponses_ids);

        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        $scores = $correcteur_manager->corriger($correcteur, $reponses);

        return $this->render("recherche/score_index.html.twig",
            ["scores" => $scores,
                "session" => $session,
                "correcteur" => $correcteur,
                "reponses" => $reponses]);
    }
    #[Route("/score/csv/{session_id}/{correcteur_id}", name: "score_csv")]
    public function score_csv(
        SessionRepository    $session_repository,
        CorrecteurRepository $correcteur_repository,
        CorrecteurManager    $correcteur_manager,
        CsvScoreManager      $csv_score_manager,
        int                  $session_id,
        int                  $correcteur_id,
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage,
        ReponseCandidatRepository      $reponse_candidat_repository,
    ): Response
    {
        $cached_reponses_ids = $reponses_candidat_session_storage->get();
        $reponses = $reponse_candidat_repository->findAllByIds($cached_reponses_ids);

        $session = $session_repository->find($session_id);
        $correcteur = $correcteur_repository->find($correcteur_id);

        $scores = $correcteur_manager->corriger($correcteur, $reponses);

        return $csv_score_manager->export($session, $correcteur->profil, $scores);
    }

    #[Route("/profil/{session_id}/{correcteur_id}", name: "profil")]
    public function profil(
        int                  $session_id,
        int                  $correcteur_id,
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage,
        ReponseCandidatRepository      $reponse_candidat_repository,
    ): Response
    {
        $cached_reponses_ids = $reponses_candidat_session_storage->get();
        $reponses = $reponse_candidat_repository->findAllByIds($cached_reponses_ids);

        return $this->redirectToRoute("calcul_profil_score_form", ['session_id' => $session_id, 'correcteur_id' => $correcteur_id, 'reponsesRecherche' => $reponses]);
    }
    #[Route("/enlever/reponse/{id}", "enlever_reponse")]
    public function removeReponseCandidat(ReponsesCandidatSessionStorage $reponses_candidat_session_storage, int $id): RedirectResponse
    {
        $cached_reposes = $reponses_candidat_session_storage->get();
        $reponses_candidat_session_storage->set(array_diff($cached_reposes, [$id]));
        $this->addFlash("success", "Le candidat a été retiré.");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/index", name: "index")]
    public function index(
        ReponsesCandidatSessionStorage $reponses_candidat_session_storage,
        FiltreSessionStorage           $filtre_session_storage,
        Request                        $request,
        ReponseCandidatRepository      $reponse_candidat_repository
    ): BinaryFileResponse|RedirectResponse|Response
    {
        $filtre = $filtre_session_storage->getOrSetDefault(new RechercheFiltre(filtre_prenom: "",
            filtre_nom: "",
            filtre_date_de_naissance_min: new DateTime("@1344988800"),
            filtre_date_de_naissance_max: new DateTime("now"),
            niveau_scolaire: null,
            session: null
        ));

        $cached_reponses_ids = $reponses_candidat_session_storage->getOrSetDefault(array());
        $cached_reponses = $reponse_candidat_repository->findAllByIds($cached_reponses_ids);

        $reponse_candidats_checked = array_map(
            function (ReponseCandidat $reponse_candidat) use ($cached_reponses_ids) {
                return new ReponseCandidatChecked($reponse_candidat,
                    in_array($reponse_candidat->id, $cached_reponses_ids));
            },
            $reponse_candidat_repository->filtrer($filtre)
        );

        $recherche_reponses_candidat = new RechercheReponsesCandidat(
            reponses_candidat: $reponse_candidats_checked);
        $form_reponses = $this->createForm(RechercheReponsesCandidatType::class, $recherche_reponses_candidat);
        $form_filtre = $this->createForm(RechercheFiltreType::class, $filtre);

        $form_reponses->handleRequest($request);
        if ($form_reponses->isSubmitted() and $form_reponses->isValid()) {

            /** @var int[] $to_add */
            $to_add = [];

            foreach ($recherche_reponses_candidat->reponses_candidat as $reponse_candidat_checked) {

                if ($reponse_candidat_checked->checked) {
                    $to_add[] = $reponse_candidat_checked->reponse_candidat->id;
                }

                $reponses_candidat_session_storage->set(
                    array_merge($reponses_candidat_session_storage->get(), $to_add)
                );
            }

            return $this->redirectToRoute("recherche_index");
        }

        $form_filtre->handleRequest($request);
        if ($form_filtre->isSubmitted() and $form_filtre->isValid()) {

            $filtre_session_storage->set($filtre);

            return $this->redirectToRoute("recherche_index");
        }

        return $this->render("recherche/index.html.twig",
            ["selectionnes" => $cached_reponses,
                "form_filtre" => $form_filtre->createView(),
                "form_reponses" => $form_reponses->createView()]);
    }

}