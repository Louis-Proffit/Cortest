<?php

namespace App\Core\Etalonnage;

use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;

class EtalonnageManager
{
    private const PERCENTILES_NORMAL = array(-5, -2.33 ,  -2.05 ,  -1.88 ,  -1.75 ,  -1.64 ,  -1.55 ,  -1.48 ,  -1.41 ,
        -1.34 ,  -1.28 ,  -1.23 ,  -1.17 ,  -1.13 ,  -1.08 ,  -1.04 ,  -0.99 ,  -0.95 ,  -0.92 ,
        -0.88 ,  -0.84 ,  -0.81 ,  -0.77 ,  -0.74 ,  -0.71 ,  -0.67 ,  -0.64 ,  -0.61 ,  -0.58 ,
        -0.55 ,  -0.52 ,  -0.5 ,  -0.47 ,  -0.44 ,  -0.41 ,  -0.39 ,  -0.36 ,  -0.33 ,  -0.31 ,
        -0.28 ,  -0.25 ,  -0.23 ,  -0.2 ,  -0.18 ,  -0.15 ,  -0.13 ,  -0.1 ,  -0.08 ,  -0.05 ,
        -0.03 ,  0.0 ,  0.03 ,  0.05 ,  0.08 ,  0.1 ,  0.13 ,  0.15 ,  0.18 ,  0.2 ,  0.23 ,  0.25 ,
        0.28 ,  0.31 ,  0.33 ,  0.36 ,  0.39 ,  0.41 ,  0.44 ,  0.47 ,  0.5 ,  0.52 ,  0.55 ,  0.58 ,
        0.61 ,  0.64 ,  0.67 ,  0.71 ,  0.74 ,  0.77 ,  0.81 ,  0.84 ,  0.88 ,  0.92 ,  0.95 ,  0.99 ,
        1.04 ,  1.08 ,  1.13 ,  1.17 ,  1.23 ,  1.28 ,  1.34 ,  1.41 ,  1.48 ,  1.55 ,  1.64 ,  1.75 ,
        1.88 ,  2.05 ,  2.33);

    /**
     * @param Etalonnage $etalonnage
     * @param float[][] $scores
     * @return array
     */
    public function etalonner(Etalonnage $etalonnage, array $scores): array
    {
        $etalonne = [];

        foreach ($scores as $reponseId => $score) {

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

            $etalonne[$reponseId] = $result;
        }

        return $etalonne;
    }

    public function calculateBounds(float $mean, float $stdDev, int $boundsNumber): array
    {
        $bounds = [];
        for ($i = 1; $i <= $boundsNumber; $i++) {
            $percentile = $i / ($boundsNumber + 1);
            $value = $this->inversNormalPdf($percentile, $mean, $stdDev);
            $bounds[] = $value;
        }
        return $bounds;
    }

    private function inversNormalPdf(float $p, float $mu, float $sigma): float
    {
        $pdfValue = self::PERCENTILES_NORMAL[intval(round($p, 2) * 100)];
        return $pdfValue * $sigma + $mu;
    }
}