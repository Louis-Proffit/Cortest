<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Functions;

use App\Core\Exception\MissingEchelleException;
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
        if (!array_key_exists(CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle, $arguments))
        {
            throw new MissingEchelleException($echelle, "L'échelle " . $echelle .
                " est utilisée dans la correction mais n'existe pas. Vérifiez les occurrences de cette échelle dans " .
                "la correction que vous venez d'utiliser et remplacez-les par le nouveau nom de cette échelle, " .
                "ou créez cette échelle dans la structure que vous utilisez.");
        }
        return $arguments[CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle];
    }

    public function compiler($echelle): string
    {
        return CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle;
    }
}