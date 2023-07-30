<?php

namespace App\Core\ScoreBrut;

use App\Core\ReponseCandidat\ExportReponseCandidat;
use App\Entity\Echelle;
use App\Entity\ReponseCandidat;
use App\Entity\Structure;

/**
 * Fonctions d'export de scores.
 * On peut changer ici les intitulés des colonnes des fichiers produits, essentiellement .csv
 */
readonly class ExportScoresBruts
{
    public function __construct(
        private ExportReponseCandidat $exportReponseCandidat
    )
    {
    }

    /**
     * Exporte des scores sous forme d'array en 6 colonnes
     * @param Structure $structure
     * @param ScoresBruts $scoresBruts
     * @param ReponseCandidat[] $reponsesCandidat
     * @return array
     */
    public function export(Structure $structure, ScoresBruts $scoresBruts, array $reponsesCandidat): array
    {
        $data = [];

        /** @var ReponseCandidat $reponse */
        foreach ($reponsesCandidat as $reponseCandidat) {
            $toAdd = $this->exportReponseCandidat->export(reponsesCandidat: $reponseCandidat, questions: null);

            $scoreBrut = $scoresBruts->get($reponseCandidat);

            /** @var Echelle $echelle */
            foreach ($structure->echelles as $echelle) {
                $toAdd[$echelle->nom] = $scoreBrut->get($echelle);
            }

            $data[] = $toAdd;
        }

        return $data;
    }
}