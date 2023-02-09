<?php

namespace App\Controller;

use App\Core\Files\Csv\CsvReponseManager;
use App\Entity\ReponseCandidat;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\Data\RechercheReponsesCandidat;
use App\Form\RechercheReponsesCandidatType;
use App\Repository\ReponseCandidatRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

#[Route("/recherche", name: "recherche_")]
class RechercheController extends AbstractController
{
    const REPONSES_CANDIDATS_IDS_SESSION_KEY = "reponses_candidats_ids";

    protected function setEmptyCacheIfNotExists(
        SessionInterface $session
    )
    {
        if (!$session->has(self::REPONSES_CANDIDATS_IDS_SESSION_KEY)) {
            $session->set(self::REPONSES_CANDIDATS_IDS_SESSION_KEY, []);
        }
    }

    /**
     * @return int[]
     */
    protected function getCachedReponseIds(
        SessionInterface $session
    ): array
    {
        $this->setEmptyCacheIfNotExists($session);
        return $session->get(self::REPONSES_CANDIDATS_IDS_SESSION_KEY);
    }

    /**
     * @return ReponseCandidat[]
     */
    protected function getCachedReponses(
        SessionInterface          $session,
        ReponseCandidatRepository $reponse_candidat_repository
    ): array
    {
        return $reponse_candidat_repository->findAllByIds($this->getCachedReponseIds($session));
    }

    protected function addAllReponses(
        SessionInterface $session,
        array            $ids)
    {
        /** @var int[] $cached_reponse_ids */
        $cached_reponse_ids = $session->get(self::REPONSES_CANDIDATS_IDS_SESSION_KEY);

        $session->set(self::REPONSES_CANDIDATS_IDS_SESSION_KEY, array_merge($cached_reponse_ids, $ids));
    }

    protected function removeReponse(
        SessionInterface $session,
        int              $id)
    {
        /** @var int[] $cached_reponse_ids */
        $cached_reponse_ids = $session->get(self::REPONSES_CANDIDATS_IDS_SESSION_KEY);
        $session->set(self::REPONSES_CANDIDATS_IDS_SESSION_KEY, array_diff($cached_reponse_ids, [$id]));
    }

    #[Route("/download/reponses", name: "download_reponses")]
    public function downloadReponses(
        SessionInterface          $session,
        ReponseCandidatRepository $reponse_candidat_repository,
        CsvReponseManager         $csv_reponse_manager,
    ): BinaryFileResponse
    {
        $cached_reponses = $this->getCachedReponses($session, $reponse_candidat_repository);
        return $csv_reponse_manager->export($cached_reponses, "export_recherche_reponses.csv");
    }

    #[Route("/calculer/scores", name: "calculer_scores")]
    public function calculerScores(): Response
    {
        $this->addFlash("warning", "Pas encore implémenté");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/enlever/reponse/{id}", "enlever_reponse")]
    public function removeReponseCandidat(SessionInterface $session, int $id): RedirectResponse
    {
        $this->removeReponse($session, $id);
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/index", name: "index")]
    public function index(
        SessionInterface          $session,
        Request                   $request,
        ReponseCandidatRepository $reponse_candidat_repository
    ): BinaryFileResponse|RedirectResponse|Response
    {
        $cached_reponses_ids = $this->getCachedReponseIds($session);
        $cached_reponses = $this->getCachedReponses($session, $reponse_candidat_repository);

        $reponse_candidats_checked = array_map(
            function (ReponseCandidat $reponse_candidat) use ($cached_reponses_ids) {
                return new ReponseCandidatChecked($reponse_candidat,
                    in_array($reponse_candidat->id, $cached_reponses_ids));
            },
            $reponse_candidat_repository->findAll()
        );

        $recherche_reponses_candidat = new RechercheReponsesCandidat(
            filtre_prenom: "",
            filtre_nom: "",
            filtre_date_de_naissance_min: new DateTime("@1344988800"),
            filtre_date_de_naissance_max: new DateTime("now"),
            reponses_candidat: $reponse_candidats_checked);
        $form = $this->createForm(RechercheReponsesCandidatType::class, $recherche_reponses_candidat);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            /** @var ClickableInterface $save_button */
            $save_button = $form->get("save");
            /** @var ClickableInterface $filter_button */
            $filter_button = $form->get("filter");

            if ($save_button->isClicked()) {

                /** @var int[] $toAdd */
                $toAdd = [];

                foreach ($recherche_reponses_candidat->reponses_candidat as $reponse_candidat_checked) {

                    if ($reponse_candidat_checked->checked) {
                        $toAdd[] = $reponse_candidat_checked->reponse_candidat->id;
                    }

                    $this->addAllReponses($session, $toAdd);

                    return $this->redirectToRoute("recherche_index");
                }
            } else if ($filter_button->isClicked()) {

                $to_show = $reponse_candidat_repository->filter(
                    nom_filter: $recherche_reponses_candidat->filtre_nom,
                    prenom_filter: $recherche_reponses_candidat->filtre_prenom,
                    date_naissance_min: $recherche_reponses_candidat->filtre_date_de_naissance_min,
                    date_de_naissance_max: $recherche_reponses_candidat->filtre_date_de_naissance_max
                );

                $to_show_checked = array_map(
                    function (ReponseCandidat $reponse_candidat) use ($cached_reponses_ids) {
                        return new ReponseCandidatChecked($reponse_candidat,
                            in_array($reponse_candidat->id, $cached_reponses_ids));
                    },
                    $to_show
                );

                // TODO ne pas faire une affectation ici, il faut pas changer le champ mais intégrer l'attribut "recherché" à l'objet
                $recherche_reponses_candidat->reponses_candidat = $to_show_checked;

                $this->render("recherche/index.html.twig",
                    ["selectionnes" => $cached_reponses, "form" => $form->createView()]);

            } else {
                throw new UnexpectedValueException("Form submit par un bouton inconnu");
            }
        }

        return $this->render("recherche/index.html.twig",
            ["selectionnes" => $cached_reponses, "form" => $form->createView()]);
    }

}