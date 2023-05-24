<?php

namespace App\Core\Grille\Values;

use App\Core\Grille\Grille;

/**
 * Type de grille créée pour le concours de brigadier de police
 */
class GrilleBrigadierDePolice extends Grille
{

    public function __construct()
    {
        parent::__construct(nom: "Grille brigadier de police", nombre_questions: 120);
    }
}