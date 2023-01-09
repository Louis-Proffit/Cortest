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
            new Property("Prénom", "prenom"),
            new Property("Nom de jeune fille", "nom_jeune_fille"),
            new Property("Niveau scolaire", "niveau_scolaire"),
            new Property("Date de naissance", "date_naissance"),
            new Property("Sexe", "sexe"),
            new Property("Concours", "concours"),
            new Property("SGAP ou CS", "sgap"),
            new Property("Date d'examen", "date_examen"),
            new Property("Type de concours", "type_concours"),
            new Property("Version batterie", "version_batterie"),
            new Property("Réservé", "reserve"),
            new Property("Autre 1", "autre_1"),
            new Property("Autre 2", "autre_2"),
            new Property("Réponses", "reponses")
        ];
    }
}