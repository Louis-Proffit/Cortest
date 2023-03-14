<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;

class Repondu extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "repondu(numéro_question)",
            nom_php: "repondu",
            description: '
            <p>Renvoie 1 si le candidat a choisi répondu à la question, 0 sinon</p>
            ',
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 1, 1, 1, 1, 1);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 1, 1, 1, 1, 1);
    }
}