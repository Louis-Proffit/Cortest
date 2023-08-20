<?php

namespace App\Core\ScoreEtalonne;

use App\Core\ScoreBrut\ScoresBruts;
use App\Entity\Etalonnage;
use App\Entity\ReponseCandidat;

class ScoresEtalonnes
{

    /**
     * @param ScoreEtalonne[] $__array
     */
    public function __construct(
        readonly Etalonnage  $etalonnage,
        readonly ScoresBruts $scoresBruts,
        private array        $__array = []
    )
    {
    }

    public function get(ReponseCandidat $reponseCandidat): ScoreEtalonne
    {
        return $this->__array[$reponseCandidat->id];
    }

    public function set(ReponseCandidat $reponseCandidat, ScoreEtalonne $scoreBrut): void
    {
        $this->__array[$reponseCandidat->id] = $scoreBrut;
    }

}