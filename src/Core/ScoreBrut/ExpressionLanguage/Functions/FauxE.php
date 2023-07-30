<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class FauxE extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "fauxE(numéro_question)",
            nom_php: "fauxE",
            description: "Renvoie 0 si le candidat à choisi l'item E à la question d'indice [indice], 1 sinon",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 1, 1, 1, 1, 0);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 1, 1, 1, 1, 0);
    }
}