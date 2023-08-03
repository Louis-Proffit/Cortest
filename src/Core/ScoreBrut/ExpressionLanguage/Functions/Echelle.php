<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\ScoreBrut\ExpressionLanguage\CortestFunction;

class Echelle extends CortestFunction
{

    public function __construct()
    {
        parent::__construct(nom_affiche: "echelle('nom_echelle_php')",
            nom_php: "echelle",
            description: "
            <p>Renvoie le score_brut obtenu à l'échelle [nom_echelle]</p>
            <ul>
            <li>echelle(x) : score_brut obtenu à l'échelle x</li>
           </ul>
            ",
            evaluator: $this->evaluer(...),
            compiler: $this->compiler(...));
    }

    public function evaluer($arguments, $echelle): float
    {
        return $arguments[CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle];
    }

    public function compiler($echelle): string
    {
        return CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle;
    }
}