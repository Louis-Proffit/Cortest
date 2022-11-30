<?php

namespace App\Core\Entities;

/**
 * Calcul d'un score à partir des réponses d'un candidat
 */
abstract class ScoreComputer
{

    /**
     * @phpstan-param Reponse $reponses les réponses du candidat
     * @return ProfilOuScore le score du candidat
     */
    abstract function compute($reponses): ProfilOuScore;
}