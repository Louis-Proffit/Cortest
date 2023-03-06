<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;
use Closure;

class VraiD extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "vraiD(numéro_question)",
            nom_php: "vraiD",
            description: "Renvoie 1 si le candidat à choisi l'item D à la question d'indice [indice], 0 sinon",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 0, 0,0,1, 0);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 0, 0,1,0, 0);
    }
}