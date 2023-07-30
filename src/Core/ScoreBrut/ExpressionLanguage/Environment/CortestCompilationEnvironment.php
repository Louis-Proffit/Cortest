<?php

namespace App\Core\ScoreBrut\ExpressionLanguage\Environment;

use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Entity\Echelle;

readonly class CortestCompilationEnvironment
{

    public function __construct(private array $types)
    {
    }

    /**
     * Renvoie true si le résultat de type $dependency peut être utilisé pour le calcul du type $type
     * @param string $type
     * @param string $dependency
     * @return bool
     */
    private function dependsOn(string $type, string $dependency): bool
    {
        $orderType = Echelle::TYPE_ECHELLE_HIERARCHY[$type];
        $orderDependency = Echelle::TYPE_ECHELLE_HIERARCHY[$dependency];
        return $orderDependency < $orderType;
    }

    public function compileEnvironment(string $echelleType): array
    {
        $environment = [CortestExpressionLanguage::ENVIRONMENT_KEY_REPONSES];

        foreach ($this->types as $echelle => $type) {
            if ($this->dependsOn($echelleType, $type)) {
                $environment[] = CortestExpressionLanguage::ENVIRONMENT_KEY_ECHELLE_PREFIX . $echelle;
            }
        }

        return $environment;
    }
}