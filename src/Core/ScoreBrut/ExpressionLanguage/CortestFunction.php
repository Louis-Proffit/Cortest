<?php

namespace App\Core\ScoreBrut\ExpressionLanguage;

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

    protected function innerEvaluer(array $arguments, int $index, float $siVide, float $siA, float $siB, float $siC, float $siD, float $siE): float
    {
        $trueIndex = $index - 1;
        return match ($arguments[CortestExpressionLanguage::ENVIRONMENT_KEY_REPONSES][$trueIndex]) {
            0 => $siVide,
            1 => $siA,
            2 => $siB,
            3 => $siC,
            4 => $siD,
            5 => $siE,
            default => 0
        };
    }

    protected function innerCompiler(int $index, float $siVide, float $siA, float $siB, float $siC, float $siD, float $siE): string
    {
        $trueIndex = $index - 1;
        return sprintf('(reponses[%1$d] == 0 ? %2$d : (reponses[%1$d] == 1 ? %3$d : (reponses[%1$d] == 2 ? %4$d : (reponses[%1$d] == 3 ? %5$d : (reponses[%1$d] == 4 ? %6$d : %7$d)))))',
            $trueIndex,
            $siVide,
            $siA,
            $siB,
            $siC,
            $siD,
            $siE);
    }

    public function toExpressionFunction(): ExpressionFunction
    {
        return new ExpressionFunction(
            name: $this->nom_php,
            compiler: $this->compiler,
            evaluator: $this->evaluator
        );
    }


}