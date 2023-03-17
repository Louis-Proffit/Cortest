<?php

namespace App\Core\Correcteur\ExpressionLanguage;

use App\Core\Correcteur\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
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

    protected function innerEvaluer(array $arguments, int $index, float $si_vide, float $si_a, float $si_b, float $si_c, float $si_d, float $si_e): float
    {
        return match ($arguments["reponses"][$index - 1]) {
            0 => $si_vide,
            1 => $si_a,
            2 => $si_b,
            3 => $si_c,
            4 => $si_d,
            5 => $si_e,
            default => 0
        };
    }

    protected function innerCompiler(int $index, float $si_vide, float $si_a, float $si_b, float $si_c, float $si_d, float $si_e): string
    {
        // TODO CHECK
        $true_index = $index - 1;
        return sprintf('(reponses[%1$d] == 0 ? %2$d : (reponses[%1$d] == 1 ? %3$d : (reponses[%1$d] == 2 ? %4$d : (reponses[%1$d] == 3 ? %5$d : (reponses[%1$d] == 4 ? %6$d : %7$d)))))',
            $true_index,
            $si_vide,
            $si_a,
            $si_b,
            $si_c,
            $si_d,
            $si_e);
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