<?php

namespace App\Core\Correcteur\ExpressionLanguage;

use App\Core\Correcteur\ExpressionLanguage\Environment\CortestExpressionEnvironment;
use Closure;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

class CortestFunction
{

    public string $nom_affiche;
    public string $nom_php;
    public string $description;
    public Closure $evaluator;
    public Closure $compiler;

    /**
     * @param string $nom_affiche
     * @param string $nom_php
     * @param string $description
     * @param Closure $evaluator
     * @param Closure $compiler
     */
    public function __construct(string $nom_affiche, string $nom_php, string $description, Closure $evaluator, Closure $compiler)
    {
        $this->nom_affiche = $nom_affiche;
        $this->nom_php = $nom_php;
        $this->description = $description;
        $this->evaluator = $evaluator;
        $this->compiler = $compiler;
    }

    protected function innerEvaluer(array $arguments, int $index, float $si_0, float $si_1, float $si_2, float $si_3, float $si_4, float $si_5): float
    {
        return match ($arguments["reponses"][$index - 1]) {
            0 => $si_0,
            1 => $si_1,
            2 => $si_2,
            3 => $si_3,
            4 => $si_4,
            5 => $si_5,
            default => 0
        };
    }

    protected function innerCompiler(int $index, float $si_0, float $si_1, float $si_2, float $si_3, float $si_4, float $si_5): string
    {
        $true_index = $index - 1;
        return sprintf('(reponses[%1$d] == 0 ? %2$d : (reponses[%1$d] == 1 ? %3$d : (reponses[%1$d] == 2 ? %4$d : (reponses[%1$d] == 3 ? %5$d : (reponses[%1$d] == 4 ? %6$d : %7$d)))))',
            $true_index,
            $si_0,
            $si_1,
            $si_2,
            $si_3,
            $si_4,
            $si_5);
    }

    public function to_expression_function(): ExpressionFunction
    {
        return new ExpressionFunction(
            $this->nom_php,
            $this->compiler,
            $this->evaluator
        );
    }


}