<?php

namespace App\Core\Correcteur\ExpressionLanguage\Functions;

use App\Core\Correcteur\ExpressionLanguage\CortestFunction;

class Echelle extends CortestFunction
{
    public function __construct()
    {
        parent::__construct(nom_affiche: "echelle(nom_echelle)",
            nom_php: "echelle",
            description: '
            <p>Renvoie le score obtenu à l\\\'echelle [nom_echelle]</p>
            <ul>
            <li>echelle(x) : score obtenu à l\\\'echelle x</li>
           </ul>
            ',
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $echelle): float
    {
        return $arguments["echelle_____" . $echelle];
    }

    public function compiler($echelle): string
    {
        return "echelle_____" . $echelle;
    }
}