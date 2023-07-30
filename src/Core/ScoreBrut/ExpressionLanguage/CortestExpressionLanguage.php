<?php

namespace App\Core\ScoreBrut\ExpressionLanguage;

use App\Core\ScoreBrut\ExpressionLanguage\Environment\CortestCompilationEnvironment;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\Echelle;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\FauxA;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\FauxB;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\FauxC;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\FauxD;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\FauxE;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\Repondu;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\Score;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\Score01234;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\Score43210;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\VraiA;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\VraiB;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\VraiC;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\VraiD;
use App\Core\ScoreBrut\ExpressionLanguage\Functions\VraiE;
use BadMethodCallException;
use JetBrains\PhpStorm\Deprecated;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CortestExpressionLanguage extends ExpressionLanguage implements ExpressionFunctionProviderInterface
{
    const ENVIRONMENT_KEY_REPONSES = "reponses";
    const ENVIRONMENT_KEY_ECHELLE_PREFIX = "__echelle_";

    public function __construct(
        private readonly VraiA      $vrai_A,
        private readonly VraiB      $vrai_B,
        private readonly VraiC      $vrai_C,
        private readonly VraiD      $vrai_D,
        private readonly VraiE      $vrai_E,
        private readonly FauxA      $faux_A,
        private readonly FauxB      $faux_B,
        private readonly FauxC      $faux_C,
        private readonly FauxD      $faux_D,
        private readonly FauxE      $faux_E,
        private readonly Score      $score,
        private readonly Score43210 $score_43210,
        private readonly Score01234 $score_01234,
        private readonly Echelle    $echelle,
        private readonly Repondu    $repondu,
        CacheItemPoolInterface      $cache = null)
    {
        parent::__construct($cache, [$this]);
    }

    /**
     * @return CortestFunction[]
     */
    public function getCortestFunctions(): array
    {
        return [
            $this->vrai_A,
            $this->vrai_B,
            $this->vrai_C,
            $this->vrai_D,
            $this->vrai_E,
            $this->faux_A,
            $this->faux_B,
            $this->faux_C,
            $this->faux_D,
            $this->faux_E,
            $this->score_01234,
            $this->score_43210,
            $this->score,
            $this->echelle,
            $this->repondu
        ];
    }

    public function getFunctions(): array
    {
        $result = [];

        foreach ($this->getCortestFunctions() as $function) {
            $result[] = $function->toExpressionFunction();
        }

        return $result;
    }

    #[Deprecated]
    public function compile(Expression|string $expression, array $names = []): string
    {
        throw new BadMethodCallException("Use compileCortest instead of compile");
    }


    public function cortestCompile(string $expression, string $type, CortestCompilationEnvironment $environment): string
    {
        return parent::compile($expression, $environment->compileEnvironment($type));
    }
}