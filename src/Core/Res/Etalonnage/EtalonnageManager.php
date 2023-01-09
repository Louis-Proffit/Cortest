<?php

namespace App\Core\Res\Etalonnage;

use App\Core\Res\ProfilOuScore\ProfilOuScore;
use App\Entity\Etalonnage;

class EtalonnageManager
{

    public function etalonner(Etalonnage $etalonnage, ProfilOuScore $profil_ou_score, array $scores): array
    {
        $etalonne = [];

        foreach ($scores as $score) {

            $result = [];

            foreach ($profil_ou_score->getProperties() as $property) {

                $etalonnage_item = $etalonnage->values[$property->nom_php]["values"];
                $score_item = $score[$property->nom_php];

                $index = 0;

                foreach ($etalonnage_item as $bound) {

                    if($score_item >= $bound) {
                        $index++;
                    } else {
                        break;
                    }
                }

                // +1 : L'indexation commence à zéro et la classe affichée commence à 1
                $result[$property->nom_php] = $index + 1;

            }

            $etalonne[] = $result;

        }

        return $etalonne;
    }

}