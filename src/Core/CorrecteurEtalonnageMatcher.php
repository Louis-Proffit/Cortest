<?php

namespace App\Core;

use App\Entity\Correcteur;
use App\Entity\Etalonnage;

/**
 * @see self::match()
 */
class CorrecteurEtalonnageMatcher
{
    /**
     * Vérifie que le cprrecteur et l'étalonnage sont compatibles
     * Pour cela, vérifie que les deux réfèrent au même profil
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @return bool
     */
    public function match(Correcteur $correcteur, Etalonnage $etalonnage): bool
    {
        return $correcteur->structure->id === $etalonnage->structure->id;
    }
}