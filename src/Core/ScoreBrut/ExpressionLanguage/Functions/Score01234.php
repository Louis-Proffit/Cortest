<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class Score01234 extends CortestFunction
{
    public function __construct()
    {
        parent::__construct(nom_affiche: "score01234(numéro_question)",
            nom_php: "score01234",
            description: "Renvoie 0, 1, 2, 3 ou 4 si la réponse est respectivement A, B, C, D ou E. Renvoie 2 en cas d'absence de réponse",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 2, 0,1,2,3,4);
    }

    public function compiler($index): string
    {
        return $this->innerCompiler($index, 2, 0,1,2,3,4);
    }
}