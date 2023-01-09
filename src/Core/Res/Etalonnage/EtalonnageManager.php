<?php

namespace App\Core\Res\Etalonnage;

use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\Etalonnage;

class EtalonnageManager
{

    public function __construct(
        private readonly ProfilOuScoreRepository $profil_ou_score_repository
    )
    {
    }

    public function etalonner(Etalonnage $etalonnage, array $scores): array
    {
        $etalonne = [];

        foreach ($scores as $reponse_id => $score) {

            $result = [];

            $profil_ou_score = $this->profil_ou_score_repository->get($etalonnage->score_id);

            foreach ($profil_ou_score->getProperties() as $property) {

                $etalonnage_item = $etalonnage->values[$property->nom_php]["values"];
                $score_item = $score[$property->nom_php];

                $index = 0;

                foreach ($etalonnage_item as $bound) {

                    if ($score_item >= $bound) {
                        $index++;
                    } else {
                        break;
                    }
                }

                // +1 : L'indexation commence à zéro et la classe affichée commence à 1
                $result[$property->nom_php] = $index + 1;

            }

            $etalonne[$reponse_id] = $result;

        }

        return $etalonne;
    }

}