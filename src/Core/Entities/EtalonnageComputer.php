<?php

namespace App\Core\Entities;

/**
 * Calcul d'un profil à partir des scores d'un candidat
 */
abstract class EtalonnageComputer
{

    /**
     * @phpstan-param ProfilOuScore $score
     */
    abstract function compute($score):ProfilOuScore;
}