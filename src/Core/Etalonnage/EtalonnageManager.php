<?php

namespace App\Core\Etalonnage;

use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;

class EtalonnageManager
{

    /**
     * @param Etalonnage $etalonnage
     * @param float[][] $scores
     * @return array
     */
    public function etalonner(Etalonnage $etalonnage, array $scores): array
    {
        $etalonne = [];

        foreach ($scores as $reponse_id => $score) {

            $result = [];

            /** @var EchelleEtalonnage $echelleEtalonnage */
            foreach ($etalonnage->echelles as $echelleEtalonnage) {

                $bounds = $echelleEtalonnage->bounds;
                $score_item = $score[$echelleEtalonnage->echelle->nom_php];

                $index = 0;

                foreach ($bounds as $bound) {

                    if ($score_item >= $bound) {
                        $index++;
                    } else {
                        break;
                    }
                }

                // +1 : L'indexation commence à zéro et la classe affichée commence à 1
                $result[$echelleEtalonnage->echelle->nom_php] = $index + 1;

            }

            $etalonne[$reponse_id] = $result;
        }

        return $etalonne;
    }

}