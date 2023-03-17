<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;
use Closure;

class FauxC extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "fauxC(numéro_question)",
            nom_php: "fauxC",
            description: "Renvoie 0 si le candidat à choisi l'item C à la question d'indice [indice], 1 sinon",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0,1,1,0,1,1);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0,1,1,0,1,1);
    }
}