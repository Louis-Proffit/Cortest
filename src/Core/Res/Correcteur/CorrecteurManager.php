<?php

namespace App\Core\Res\Correcteur;

use App\Core\Res\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Res\Grille\Grille;
use App\Core\Res\Grille\GrilleRepository;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\CandidatReponse;
use App\Entity\Correcteur;

class CorrecteurManager
{

    public function __construct(
        private readonly ProfilOuScoreRepository $profil_ou_score_repository
    )
    {
    }

    /**
     * @param Correcteur $correcteur
     * @param CandidatReponse[] $reponses
     * @return array
     */
    public function corriger(Correcteur $correcteur, array $reponses): array
    {
        $expression_language = new CortestExpressionLanguage();

        $profil_ou_score = $this->profil_ou_score_repository->get($correcteur->score_id);

        $corrige = [];

        foreach ($reponses as $id => $reponse) {
            $result = [];

            foreach ($profil_ou_score->getProperties() as $property) {

                $result[$property->nom_php] = $expression_language->evaluate(
                    $correcteur->values[$property->nom_php],
                    $this->getComputationEnv($reponse->getGrille())
                );

            }

            $corrige[$id] = $result;
        }

        return $corrige;
    }

    private function getComputationEnv(Grille $grille): array
    {
        return [
            "reponses" => $grille->reponses
        ];
    }

}