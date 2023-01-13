<?php

namespace App\Core\Correcteur;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use App\Entity\ReponseCandidat;

class CorrecteurManager
{

    /**
     * @param Correcteur $correcteur
     * @param ReponseCandidat[] $reponses_candidat
     * @return array
     */
    public function corriger(Correcteur $correcteur, array $reponses_candidat): array
    {
        $expression_language = new CortestExpressionLanguage();

        $corrige = [];

        foreach ($reponses_candidat as $reponse_candidat) {
            $result = [];

            /** @var EchelleCorrecteur $echelle */
            foreach ($correcteur->echelles as $echelle) {

                $result[$echelle->echelle->nom_php] = $expression_language->evaluate(
                    $echelle->expression,
                    $this->getComputationEnv($reponse_candidat)
                );

            }

            $corrige[$reponse_candidat->id] = $result;
        }

        return $corrige;
    }

    private function getComputationEnv(ReponseCandidat $reponse_candidat): array
    {
        return [
            "reponses" => $reponse_candidat->reponses
        ];
    }

}