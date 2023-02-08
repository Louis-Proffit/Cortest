<?php

namespace App\Controller;

use App\Core\Files\Csv\CsvManager;
use App\Core\Files\Csv\CsvReponseManager;
use App\Entity\ReponseCandidat;
use App\Form\Data\ReponsesCandidatChecked;
use App\Form\Data\ReponsesCandidatCheckedListe;
use App\Form\ReponsesCandidatCheckedListeType;
use App\Repository\ReponseCandidatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/recherche", name: "recherche_")]
class RechercheController extends AbstractController
{
    const REPONSES_CANDIDATS_IDS_SESSION_KEY = "reponses_candidats_ids";

    #[Route("/index", name: "index")]
    public function index(
        SessionInterface          $session,
        Request                   $request,
        CsvReponseManager $csv_reponse_manager,
        ReponseCandidatRepository $reponse_candidat_repository
    )
    {

        if (!$session->has(self::REPONSES_CANDIDATS_IDS_SESSION_KEY)) {
            $session->set(self::REPONSES_CANDIDATS_IDS_SESSION_KEY, []);
            $reponse_candidats_ids = [];
        } else {
            $reponse_candidats_ids = $session->get(self::REPONSES_CANDIDATS_IDS_SESSION_KEY);
        }


        $reponse_candidats = $reponse_candidat_repository->findAllByIds($reponse_candidats_ids);

        $reponse_candidats_checked = array_map(
            function (ReponseCandidat $reponse_candidat) use ($reponse_candidats_ids) {
                return new ReponsesCandidatChecked($reponse_candidat,
                    in_array($reponse_candidat->id, $reponse_candidats_ids));
            },
            $reponse_candidats
        );

        $reponse_candidats_liste = new ReponsesCandidatCheckedListe($reponse_candidats_checked);
        $form = $this->createForm(ReponsesCandidatCheckedListeType::class, $reponse_candidats_liste);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $selected = [];
            $selected_ids = [];

            foreach ($reponse_candidats_liste->reponses_candidat as $reponses_candidat_checked) {
                if ($reponses_candidat_checked->checked) {
                    $selected[] = $reponses_candidat_checked->reponses_candidat;
                    $selected_ids = $reponses_candidat_checked->reponses_candidat->id;
                }
            }

            $session->set(self::REPONSES_CANDIDATS_IDS_SESSION_KEY, $selected_ids);

            /** @var ClickableInterface $calculer_scores */
            $calculer_scores = $form->get(ReponsesCandidatCheckedListeType::CALCUL_SCORE_KEY);

            if ($calculer_scores->isClicked()) {
                // TODO
            }

            /** @var ClickableInterface $exporter_reponses_csv */
            $exporter_reponses_csv = $form->get(ReponsesCandidatCheckedListeType::EXPORTER_REPONSES_CSV_KEY);

            if ($exporter_reponses_csv->isClicked()) {

                return $csv_reponse_manager->export($selected, "export_recherche_reponses.csv");

            }

            return $this->redirectToRoute("recherche_index");
        }

        return $this->render("recherche/index.html.twig", ["form" => $form]);
    }

}