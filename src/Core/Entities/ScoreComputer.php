<?php

namespace App\Core\Entities;

/**
 * Calcul d'un score à partir des réponses d'un candidat
 */
abstract class ScoreComputer
{

    /**
     * @return ProfilOuScore le score du candidat
     */
    abstract function compute($reponses): ProfilOuScore;
}