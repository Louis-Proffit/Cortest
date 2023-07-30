<?php

namespace App\Core\ScoreEtalonne;

use App\Core\ReponseCandidat\ExportReponseCandidat;
use App\Entity\Echelle;
use App\Entity\ReponseCandidat;
use App\Entity\Structure;

readonly class ExportScoresEtalonnes
{

    public function __construct(
        private ExportReponseCandidat $exportReponseCandidat
    )
    {
    }

    /**
     * @param Structure $structure
     * @param ScoresEtalonnes $scoresEtalonnes
     * @param ReponseCandidat[] $reponses
     * @return array
     */
    public function export(Structure $structure, ScoresEtalonnes $scoresEtalonnes, array $reponses): array
    {

        $data = [];

        foreach ($reponses as $reponseCandidat) {
            $toAdd = $this->exportReponseCandidat->export(reponsesCandidat: $reponseCandidat, questions: null);

            $scoreEtalonne = $scoresEtalonnes->get($reponseCandidat);

            /** @var Echelle $echelle */
            foreach ($structure->echelles as $echelle) {
                $toAdd[$echelle->nom] = $scoreEtalonne->get($echelle);
            }

            $data[] = $toAdd;
        }

        return $data;
    }
}