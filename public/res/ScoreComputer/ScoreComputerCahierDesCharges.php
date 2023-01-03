<?php

namespace Res\ScoreComputer;

use App\Core\Entities\ProfilOuScore;
use App\Core\Entities\ScoreComputer;
use Res\DefinitionGrille\GrilleReponseEditionOctobre2019;
use Res\DefinitionScoreOuProfil\ProfilOuScoreCahierDesCharges;

class ScoreComputerCahierDesCharges extends ScoreComputer
{

    /**
     * @param GrilleReponseEditionOctobre2019 $reponses
     */
    function compute($reponses): ProfilOuScore
    {
        return new ProfilOuScoreCahierDesCharges(
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
        );
    }
}