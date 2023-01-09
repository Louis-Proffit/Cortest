<?php

namespace App\Core\Res\Correcteur\CortestDsl;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CortestExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{

    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                "vrai1",
                fn() => null,
                function ($arguments, $value) {
                    $reponse = $arguments["reponses"][$value - 1];
                    if ($reponse == 0) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            ),
            new ExpressionFunction(
                "vrai2",
                fn() => null,
                function ($arguments, $value) {
                    $reponse = $arguments["reponses"][$value - 1];
                    if ($reponse == 1) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            )
        ];
    }
}