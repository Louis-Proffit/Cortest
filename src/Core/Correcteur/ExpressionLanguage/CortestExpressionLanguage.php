<?php

namespace App\Core\Correcteur\ExpressionLanguage;

use App\Core\Correcteur\ExpressionLanguage\Environment\CortestCompilationEnvironment;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
use App\Core\Correcteur\ExpressionLanguage\Functions\Echelle;
use App\Core\Correcteur\ExpressionLanguage\Functions\Score;
use App\Core\Correcteur\ExpressionLanguage\Functions\Vrai1;
use App\Core\Correcteur\ExpressionLanguage\Functions\Vrai2;
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
        private readonly Vrai1   $vrai_1,
        private readonly Vrai2   $vrai_2,
        private readonly Score   $score,
        private readonly Echelle $echelle,
        CacheItemPoolInterface   $cache = null)
    {
        parent::__construct($cache, [$this]);
    }

    /**
     * @return CortestFunction[]
     */
    public function getCortestFunctions(): array
    {
        return [
            $this->vrai_1,
            $this->vrai_2,
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