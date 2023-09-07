<?php

namespace App\Core\ScoreBrut;

use App\Core\Exception\CalculScoreBrutCandidatException;
use App\Core\Exception\CalculScoreBrutException;
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
     * @throws CalculScoreBrutCandidatException
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


            try {
                $scores = $cortestEvaluationEnvironment->computeScores();
                $corrige->set($reponseCandidat, $scores);
            } catch (CalculScoreBrutException $e) {
                throw new CalculScoreBrutCandidatException($reponseCandidat, $e);
            }
        }

        return $corrige;
    }
}