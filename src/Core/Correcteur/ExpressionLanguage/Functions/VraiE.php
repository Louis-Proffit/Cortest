<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;

class VraiE extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "vraiE(numéro_question)",
            nom_php: "vraiE",
            description: "Renvoie 1 si le candidat à choisi l'item E à la question d'indice [indice], 0 sinon",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 0, 0, 0, 0, 1);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 0, 0, 0, 0, 1);
    }
}