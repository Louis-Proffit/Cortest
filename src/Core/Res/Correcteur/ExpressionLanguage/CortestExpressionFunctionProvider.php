<?php

namespace App\Core\Res\Correcteur\ExpressionLanguage;

use App\Core\Res\Correcteur\ExpressionLanguage\Functions\Vrai1;
use App\Core\Res\Correcteur\ExpressionLanguage\Functions\Vrai2;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CortestExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{

    private array $functions;

    public function __construct()
    {
        $this->functions = [
            new Vrai1(),
            new Vrai2()
        ];
    }

    public function getFunctions(): array
    {
        return array_map(
            fn(CortestFunction $cortest_function) => $this->fromCortestFunction($cortest_function),
            $this->functions
        );
    }

    private function fromCortestFunction(CortestFunction $cortest_function): ExpressionFunction
    {
        return new ExpressionFunction(
            $cortest_function->nom_php(),
            $this->emptyCallable(),
            $cortest_function->evaluator()
        );
    }

    private function emptyCallable(): callable
    {
        return fn() => null;
    }

}