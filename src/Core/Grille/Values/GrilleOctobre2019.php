<?php

namespace App\Core\Grille\Values;

use App\Core\Grille\Grille;

class GrilleOctobre2019 extends Grille
{

    public function __construct()
    {
        parent::__construct(nom: "Grille d'octobre 2019", nombre_questions: 640);
    }
}