<?php

namespace App\Core\Grille\Values;

use App\Core\Grille\Grille;

/**
 * Type de grille crée en octobre 2019
 */
class GrilleOctobre2019 extends Grille
{

    public function __construct()
    {
        parent::__construct(nom: "Grille d'octobre 2019", nombre_questions: 640);
    }
}