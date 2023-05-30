<?php

namespace App\Controller;

use App\Core\Reponses\FiltreSessionStorage;
use App\Core\Reponses\ReponsesCandidatSessionStorage;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Entity\ReponseCandidat;
use App\Form\Data\RechercheFiltre;
use App\Form\Data\RechercheReponsesCandidat;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\RechercheFiltreType;
use App\Form\RechercheReponsesCandidatType;
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
    const LOWEST_TIME = "@1344988800";

    #[Route("/vider", name: "vider")]
    public function vider(ReponsesCandidatStorage $reponsesCandidatStorage): Response
    {
        $reponsesCandidatStorage->set(array());
        $this->addFlash("success", "Les candidats ont été retirés, vous pouvez en sélectionner de nouveaux.");
        return $this->redirectToRoute("recherche_index");
    }

    /**
     * TODO ne pas passer par sessionStorage, mais par storage directement (encapsulation correcte)
     * @param ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage
     * @param int $reponse_id
     * @return RedirectResponse
     */
    #[Route("/deselectionner/{reponse_id}", "deselectionner")]
    public function removeReponseCandidat(
        ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage,
        int                            $reponse_id): RedirectResponse
    {
        $cached_reposes = $reponsesCandidatSessionStorage->get();
        $reponsesCandidatSessionStorage->set(array_diff($cached_reposes, array($reponse_id)));
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
            filtre_date_de_naissance_min: new DateTime(self::LOWEST_TIME),
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

        $recherche_reponses_candidat = new RechercheReponsesCandidat(reponses_candidat: $reponse_candidats_checked);
        $form_reponses = $this->createForm(RechercheReponsesCandidatType::class, $recherche_reponses_candidat);
        $formFiltre = $this->createForm(RechercheFiltreType::class, $filtre);

        $form_reponses->handleRequest($request);
        if ($form_reponses->isSubmitted() and $form_reponses->isValid()) {

            /** @var int[] $to_add */
            $initial = $reponses_candidat_session_storage->get();

            foreach ($recherche_reponses_candidat->reponses_candidat as $reponse_candidat_checked) {

                if ($reponse_candidat_checked->checked) {
                    $initial[] = $reponse_candidat_checked->reponse_candidat->id;
                }
            }

            $reponses_candidat_session_storage->set($initial);

            return $this->redirectToRoute("recherche_index");
        }

        $formFiltre->handleRequest($request);
        if ($formFiltre->isSubmitted() and $formFiltre->isValid()) {

            $filtre_session_storage->set($filtre);

            return $this->redirectToRoute("recherche_index");
        }

        return $this->render("recherche/index.html.twig",
            ["selectionnes" => $cached_reponses,
                "form_filtre" => $formFiltre->createView(),
                "form_reponses" => $form_reponses->createView()]);
    }

}