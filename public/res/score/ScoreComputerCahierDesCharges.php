<?php

namespace App\Core\Entities;

class ScoreComputerCahierDesCharges extends EtalonnageComputer
{

    /**
     * @param ReponseEditionOctobre2019 $reponses
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