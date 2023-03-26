<?php

namespace App\Core\Files\Csv;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvScoreManager
{

    public function __construct(
        private readonly CsvManager $csv_manager
    )
    {
    }

    protected function file_name(Session $session): string
    {
        return "scores_session_" . $session->date->format("d-m-Y") . "_" . $session->concours->nom . ".csv";
    }

    public function export(Session $session, Profil $profil, array $scores): BinaryFileResponse
    {
        $data = [];

        /** @var ReponseCandidat $reponse */
        foreach ($session->reponses_candidats as $reponse) {
            $toAdd = [
                "Code barre" => $reponse->code_barre,
                "Nom" => $reponse->nom,
                "Prenom" => $reponse->prenom,
                "Nom de jeune fille" => $reponse->nom_jeune_fille,
                "Date de naissance" => $reponse->date_de_naissance->format("d/m/Y"),
                "Type concours" => $reponse->eirs,
            ];

            $candidat_scores = $scores[$reponse->id];
            /** @var Echelle $echelle */
            foreach ($profil->echelles as $echelle) {
                $toAdd[$echelle->nom] = $candidat_scores[$echelle->nom_php];
            }

            $data[] = $toAdd;
        }


        return $this->csv_manager->export($data, $this->file_name($session));
    }
}