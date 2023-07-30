<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class VraiA extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "vraiA(numéro_question)",
            nom_php: "vraiA",
            description: '
            <p>Renvoie 1 si le candidat a choisi l\\\'item A à la question d\\\'indice [indice], 0 sinon</p>
            <ul>
            <li>vraiA(3) : 1 point si le candidat a répondu A à la question 3, 0 sinon</li>
            <li>vraiA() : Erreur de syntaxe</li>
            <li>vraiA(3, 5) : Le 5 ne sera pas lu</li>
           </ul>
            ',
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index): int
    {
        return $this->innerEvaluer($arguments, $index, 0, 1, 0, 0, 0, 0);
    }


    public function compiler($index): string
    {
        return $this->innerCompiler($index, 0, 1, 0, 0, 0, 0);
    }
}