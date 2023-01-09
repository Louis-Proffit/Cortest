<?php

namespace App\Core\Res\Grille\Values;

use App\Core\Res\Grille\Grille;
use App\Core\Res\Property;

class GrilleBrigadierDePolice implements Grille
{

    public function getNom(): string
    {
        return "Grille d'accès au grade 'Brigadier de Police'";
    }

    public function getProperties(): array
    {
        return [
            new Property("Numéro de candidat", "numero_candidat"),
            new Property("SGAP", "sgap"),
            new Property("Réponses", "reponses"),
        ];
    }
}