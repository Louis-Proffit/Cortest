<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class VraiB extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "vraiB(numéro_question)",
            nom_php: "vraiB",
            description: "Renvoie 1 si le candidat à choisi l'item B à la question d'indice [indice], 0 sinon",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 0, 1, 0, 0, 0);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 0, 1, 0, 0, 0);
    }
}