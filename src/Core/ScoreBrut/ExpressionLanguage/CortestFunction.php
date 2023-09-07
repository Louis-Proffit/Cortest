<?php

namespace App\Core\ScoreBrut\ExpressionLanguage;

use App\Core\Exception\CalculScoreBrutException;
use App\Entity\ReponseCandidat;
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

    /**
     * @throws CalculScoreBrutException
     */
    protected function innerEvaluer(array $arguments, int $index, float $siVide, float $siA, float $siB, float $siC, float $siD, float $siE): float
    {
        $trueIndex = $index - 1;
        $reponses = $arguments[CortestExpressionLanguage::ENVIRONMENT_KEY_REPONSES];
        if (!in_array($trueIndex, $reponses)) {
            throw new CalculScoreBrutException(message: "La réponse d'indice " . $index . " n'existe pas");
        }
        $reponse = $reponses[$trueIndex];
        return match ($reponse) {
            0 => $siVide,
            1 => $siA,
            2 => $siB,
            3 => $siC,
            4 => $siD,
            5 => $siE,
            default => throw new CalculScoreBrutException("La réponse enregistrée pour l'indice $index est $reponse. Veuillez le corriger.")
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