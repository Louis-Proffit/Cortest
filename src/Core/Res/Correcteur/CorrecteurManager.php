<?php

namespace App\Core\Res\Correcteur;

use App\Core\Res\Correcteur\CortestDsl\CortestExpressionLanguage;
use App\Core\Res\Grille\Grille;
use App\Core\Res\ProfilOuScore\ProfilOuScore;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\CandidatReponse;
use App\Entity\Correcteur;
use App\Entity\Session;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CorrecteurManager
{

    public function __construct(
        private readonly ProfilOuScoreRepository $profil_ou_score_repository
    )
    {
    }

    /**
     * @param Correcteur $correcteur
     * @param CandidatReponse[] $reponses_candidats
     * @return array
     */
    public function corriger(Correcteur $correcteur, array $reponses_candidats): array
    {
        $expression_language = new CortestExpressionLanguage();

        $profil_ou_score = $this->profil_ou_score_repository->get($correcteur->score_id);

        $corrige = [];


        foreach ($reponses_candidats as $reponse) {
            $result = [];

            foreach ($profil_ou_score->getProperties() as $property) {

                $result[$property->nom_php] = $expression_language->evaluate(
                    $correcteur->values[$property->nom_php],
                    [
                        "reponses" => str_split($reponse->raw["reponses"])
                    ]
                );

            }

            $corrige[$reponse->id] = $result;
        }

        return $corrige;
    }

}