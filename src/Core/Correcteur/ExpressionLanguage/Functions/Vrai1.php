<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;

class Vrai1 extends CortestFunction
{


    public function __construct()
    {
        parent::__construct(nom_affiche: "vrai1(numéro_question)",
            nom_php: "vrai1",
            description: '
            <p>Renvoie 1 si le candidat a choisi l\\\'item 1 à la question d\\\'indice [indice], 0 sinon</p>
            <ul>
            <li>vrai1(3) : 1 point si le candidat a répondu 1 à la question 3, 0 sinon</li>
            <li>vrai1() : Erreur de syntaxe</li>
            <li>vrai1(3, 5) : Le 5 ne sera pas lu</li>
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