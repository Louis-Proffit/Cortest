<?php

namespace App\Core\Res\Grille\Values;

use App\Core\Res\Grille\Grille;
use App\Core\Res\Property;

class GrilleOctobre2019 implements Grille
{

    public function getNom(): string
    {
        return "Grille de octobre 2019";
    }

    public function getProperties(): array
    {
        return [
            new Property("Nom", "nom"),
            new Property("prenom", "prenom")
        ];
    }
}