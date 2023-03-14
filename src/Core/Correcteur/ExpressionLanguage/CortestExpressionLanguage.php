<?php

namespace App\Core\Correcteur\ExpressionLanguage;

use App\Core\Correcteur\ExpressionLanguage\Environment\CortestCompilationEnvironment;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
use App\Core\Correcteur\ExpressionLanguage\Functions\Echelle;
use App\Core\Correcteur\ExpressionLanguage\Functions\FauxA;
use App\Core\Correcteur\ExpressionLanguage\Functions\FauxB;
use App\Core\Correcteur\ExpressionLanguage\Functions\FauxC;
use App\Core\Correcteur\ExpressionLanguage\Functions\FauxD;
use App\Core\Correcteur\ExpressionLanguage\Functions\FauxE;
use App\Core\Correcteur\ExpressionLanguage\Functions\Score;
use App\Core\Correcteur\ExpressionLanguage\Functions\Score01234;
use App\Core\Correcteur\ExpressionLanguage\Functions\Score43210;
use App\Core\Correcteur\ExpressionLanguage\Functions\VraiA;
use App\Core\Correcteur\ExpressionLanguage\Functions\VraiB;
use App\Core\Correcteur\ExpressionLanguage\Functions\VraiC;
use App\Core\Correcteur\ExpressionLanguage\Functions\VraiD;
use App\Core\Correcteur\ExpressionLanguage\Functions\VraiE;
use App\Entity\EchelleCorrecteur;
use BadMethodCallException;
use JetBrains\PhpStorm\Deprecated;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use TypeError;

class CortestExpressionLanguage extends ExpressionLanguage implements ExpressionFunctionProviderInterface
{

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
            $this->echelle
        ];
    }

    public function getFunctions(): array
    {
        $result = [];

        foreach ($this->getCortestFunctions() as $function) {
            $result[] = $function->to_expression_function();
        }

        return $result;
    }

    #[Deprecated]
    public function compile(Expression|string $expression, array $names = []): string
    {
        throw new BadMethodCallException("Use compileCortest instead of compile");
    }


    public function compileCortest(string $expression, string $type, CortestCompilationEnvironment $environment): string
    {
        return parent::compile($expression, $environment->compile_environment($type));
    }
}