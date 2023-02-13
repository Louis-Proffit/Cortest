<?php

namespace App\Controller;

use App\Core\Files\Csv\CsvReponseManager;
use App\Entity\ReponseCandidat;
use App\Form\Data\RechercheFiltre;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\Data\RechercheReponsesCandidat;
use App\Form\RechercheFiltreType;
use App\Form\RechercheReponsesCandidatType;
use App\Recherche\FiltreSessionStorage;
use App\Recherche\ReponsesCandidatSessionStorage;
use App\Repository\ReponseCandidatRepository;
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

    #[Route("/calculer/scores", name: "calculer_scores")]
    public function calculerScores(): Response
    {
        $this->addFlash("warning", "Pas encore implémenté");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/enlever/reponse/{id}", "enlever_reponse")]
    public function removeReponseCandidat(ReponsesCandidatSessionStorage $reponses_candidat_session_storage, int $id): RedirectResponse
    {
        $cached_reposes = $reponses_candidat_session_storage->get();
        $reponses_candidat_session_storage->set(array_diff($cached_reposes, [$id]));
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
        $filtre = $filtre_session_storage->getOrDefault(new RechercheFiltre(filtre_prenom: "",
            filtre_nom: "",
            filtre_date_de_naissance_min: new DateTime("@1344988800"),
            filtre_date_de_naissance_max: new DateTime("now")));

        $cached_reponses_ids = $reponses_candidat_session_storage->getOrDefault(array());
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