<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Environment;

use App\Core\Exception\CalculScoreBrutException;
use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\ScoreBrut\ScoreBrut;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\ReponseCandidat;

readonly class CortestEvaluationEnvironment
{

    /**
     * @param ReponseCandidat[] $reponses
     * @param EchelleCorrecteur[] $echelles
     * @param CortestExpressionLanguage $cortestExpressionLanguage
     */
    public function __construct(private array                     $reponses,
                                private array                     $echelles,
                                private CortestExpressionLanguage $cortestExpressionLanguage)
    {
    }

    private function evaluationEnvironment(ScoreBrut $scoreBrut): array
    {
        $result = [CortestExpressionLanguage::ENVIRONMENT_KEY_REPONSES => $this->reponses];

        foreach ($scoreBrut->getAll() as $echelle => $score) {
            $result[CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle] = $score;
        }

        return $result;
    }


    /**
     * @return ScoreBrut
     * @throws CalculScoreBrutException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function computeScores(): ScoreBrut
    {
        $result = new ScoreBrut();

        foreach (Echelle::TYPE_ECHELLE_OPTIONS as $type) {

            foreach ($this->echelles as $echelle) {

                if ($type === $echelle->echelle->type) {

                    $score = $this->cortestExpressionLanguage->evaluate(
                        expression: $echelle->expression,
                        values: $this->evaluationEnvironment($result)
                    );

                    $result->set($echelle->echelle, $score);
                }
            }
        }

        return $result;
    }
}