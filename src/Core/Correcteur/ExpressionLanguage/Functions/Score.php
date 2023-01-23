<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;

class Score extends CortestFunction
{
    public function __construct()
    {
        parent::__construct(nom_affiche: "score(numéro_question, si0, si1, si2, si3, si4, si5)",
            nom_php: "score",
            description: '
            <p>Renvoie le score de l\\\'indice répondu par le candidat (0=pas de réponse) à la question [index]</p>
            <ul>
            <li>score(3,0,1,1,0,0,0) : 1 point si le candidat a répondu 1 ou 2 à la question 3, 0 sinon</li>
            <li>score() : Erreur de syntaxe</li>
            <li>score(3,1,1,1,1,1,1,9) : Le 9 ne sera pas lu</li>
           </ul>
            ',
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $index, $si0, $si1, $si2, $si3, $si4, $si5): int
    {
        return $this->innerEvaluer($arguments, $index, $si0, $si1, $si2, $si3, $si4, $si5);
    }

    public function compiler($index, $si0, $si1, $si2, $si3, $si4, $si5): string
    {
        return $this->innerCompiler($index, $si0, $si1, $si2, $si3, $si4, $si5);
    }
}