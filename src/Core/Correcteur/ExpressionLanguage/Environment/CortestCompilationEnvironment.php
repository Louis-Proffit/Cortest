<?php

namespace App\Core\Correcteur\ExpressionLanguage\Environment;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Entity\Echelle;

class CortestCompilationEnvironment
{
    public function __construct(private readonly array $types)
    {
    }

    /**
     * Renvoie true si le résultat de type $dependency peut être utilisé pour le calcul du type $type
     * @param string $type
     * @param string $dependency
     * @return bool
     */
    private function depend_on(string $type, string $dependency): bool
    {
        $order_type = Echelle::TYPE_ECHELLE_HIERARCHY[$type];
        $order_dependency = Echelle::TYPE_ECHELLE_HIERARCHY[$dependency];
        return $order_dependency < $order_type;
    }

    public function compile_environment(string $echelle_type): array
    {
        $environment = ["reponses"];

        foreach ($this->types as $echelle => $type) {
            if ($this->depend_on($echelle_type, $type)) {
                $environment[] = "echelle_____" . $echelle;
            }
        }

        return $environment;
    }
}