<?php

namespace App\Core\IO\Profil;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\ReponseCandidat;

class ExportProfils
{

    public function export(Profil $profil, array $profils, array $reponses): array
    {

        $data = [];

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

        return $data;
    }
}