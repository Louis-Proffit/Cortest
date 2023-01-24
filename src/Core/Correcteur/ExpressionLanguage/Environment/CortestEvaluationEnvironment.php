<?php

namespace App\Core\Correcteur\ExpressionLanguage\Environment;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Entity\Echelle;

class CortestEvaluationEnvironment
{
    public function __construct(private readonly array                     $reponses,
                                private readonly array                     $types,
                                private readonly array                     $expressions,
                                private readonly CortestExpressionLanguage $cortest_expression_language)
    {
    }

    private function evaluation_environment(array $echelles): array
    {
        $result = ["reponses" => $this->reponses];

        foreach ($echelles as $echelle => $score) {
            $result["echelle_____" . $echelle] = $score;
        }

        return $result;
    }


    public function compute_scores(): array
    {
        $result = [];

        foreach (Echelle::TYPE_ECHELLE_OPTIONS as $type) {

            foreach ($this->types as $echelle => $echelle_type) {

                if ($type === $echelle_type) {

                    $score = $this->cortest_expression_language->evaluate($this->expressions[$echelle],
                        $this->evaluation_environment($result));

                    $result[$echelle] = $score;

                }
            }
        }

        return $result;
    }
}