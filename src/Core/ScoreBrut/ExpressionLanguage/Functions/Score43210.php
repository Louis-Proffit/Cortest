<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class Score43210 extends CortestFunction
{
    public function __construct()
    {
        parent::__construct(nom_affiche: "score43210(numéro_question)",
            nom_php: "score43210",
            description: "Renvoie 4, 3, 2, 1 ou 0 si la réponse est respectivement A, B, C, D ou E. Renvoie 2 en cas d'absence de réponse",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 2,4,3,2,1,0);
    }

    public function compiler($index): string
    {
        return $this->innerCompiler($index, 2,4,3,2,1,0);
    }
}