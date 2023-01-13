<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;

class Vrai1 implements CortestFunction
{

    public function nom(): string
    {
        return "Vrai/faux (vrai=1)";
    }

    public function description(): string
    {
        return "Renvoie 1 si le candidat à choisi l'item 1 à la question d'indice [indice], 0 sinon";
    }

    public function evaluator(): callable
    {
        return function ($reponses, $index) {
            $reponse = $reponses[$index];
            if ($reponse == 0) {
                return 1;
            } else {
                return 0;
            }
        };
    }

    public function nom_php(): string
    {
        return "vrai1";
    }
}