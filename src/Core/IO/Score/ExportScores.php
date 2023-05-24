<?php

namespace App\Core\IO\Score;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\ReponseCandidat;

/**
 * Fonctions d'export de scores.
 * On peut changer ici les intitulés des colonnes des fichiers produits, essentiellement .csv
 */
class ExportScores
{
    const CODE_BARRE = "Code barre";
    const NOM = "Nom";
    const PRENOM = "Prenom";
    const NOM_JEUNE_FILLE = "Nom de jeune fille";
    const DATE_DE_NAISSANCE = "Date de naissance";
    const TYPE_CONCOURS = "Type concours";

    const DATE_FORMAT = "d/m/Y";

    /**
     * Exporte des scores sous forme d'array en 6 colonnes
     * @param Profil $profil
     * @param array $scores
     * @param array $reponses
     * @return array
     */
    public function export(Profil $profil, array $scores, array $reponses): array
    {
        $data = [];

        /** @var ReponseCandidat $reponse */
        foreach ($reponses as $reponse) {
            $toAdd = [
                self::CODE_BARRE => $reponse->code_barre,
                self::NOM => $reponse->nom,
                self::PRENOM => $reponse->prenom,
                self::NOM_JEUNE_FILLE => $reponse->nom_jeune_fille,
                self::DATE_DE_NAISSANCE => $reponse->date_de_naissance->format(self::DATE_FORMAT),
                self::TYPE_CONCOURS => $reponse->eirs,
            ];

            $candidat_scores = $scores[$reponse->id];
            /** @var Echelle $echelle */
            foreach ($profil->echelles as $echelle) {
                $toAdd[$echelle->nom] = $candidat_scores[$echelle->nom_php];
            }

            $data[] = $toAdd;
        }

        return $data;
    }
}