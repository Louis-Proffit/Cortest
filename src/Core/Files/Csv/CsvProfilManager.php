<?php

namespace App\Core\Files\Csv;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvProfilManager
{

    public function __construct(
        private readonly CsvManager $csv_manager
    )
    {
    }


    protected function file_name(Session $session): string
    {
        return "profils_session_" . $session->date->format("d-m-Y") . "_" . $session->concours->nom . ".csv";
    }

    public function export(Session $session, Profil $profil, array $profils, array|null $reponses=null): Response
    {
        $data = [];

        if ($reponses === null){
            $reponses = $session->reponses_candidats;
        }

        /** @var ReponseCandidat $reponse */
        foreach ($reponses as $reponse) {
            $toAdd = [
                "Nom" => $reponse->nom,
                "Prenom" => $reponse->prenom,
                "Nom de jeune fille" => $reponse->nom_jeune_fille,
                "Date de naissance" => $reponse->date_de_naissance->format("d/m/Y"),
            ];

            $candidat_profil = $profils[$reponse->id];
            /** @var Echelle $echelle */
            foreach ($profil->echelles as $echelle) {
                $toAdd[$echelle->nom] = $candidat_profil[$echelle->nom_php];
            }

            $data[] = $toAdd;
        }

        return $this->csv_manager->export($data, $this->file_name($session));
    }
}