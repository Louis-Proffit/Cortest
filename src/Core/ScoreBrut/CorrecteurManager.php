<?php

namespace App\Core\ScoreBrut;

use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\ScoreBrut\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
use App\Entity\Correcteur;
use App\Entity\ReponseCandidat;

readonly class CorrecteurManager
{


    public function __construct(
        private CortestExpressionLanguage $cortestExpressionLanguage
    )
    {
    }

    /**
     * @param Correcteur $correcteur
     * @param ReponseCandidat[] $reponseCandidats
     * @return ScoresBruts
     */
    public function corriger(Correcteur $correcteur, array $reponseCandidats): ScoresBruts
    {
        $corrige = new ScoresBruts($correcteur);

        foreach ($reponseCandidats as $reponseCandidat) {

            $cortestEvaluationEnvironment = new CortestEvaluationEnvironment(
                reponses: $reponseCandidat->reponses,
                echelles: $correcteur->echelles->toArray(),
                cortestExpressionLanguage: $this->cortestExpressionLanguage
            );


            $corrige->set($reponseCandidat, $cortestEvaluationEnvironment->computeScores());
        }

        return $corrige;
    }
}