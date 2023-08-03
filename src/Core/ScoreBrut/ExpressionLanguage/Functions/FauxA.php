<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class FauxA extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "fauxA(numéro_question)",
            nom_php: "fauxA",
            description: "
            <p>Renvoie 0 si le candidat a choisi l'item A à la question d'indice [indice], 1 sinon</p>
           </ul>
            ",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 0, 1, 1, 1, 1);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 0, 1, 1, 1, 1);
    }
}