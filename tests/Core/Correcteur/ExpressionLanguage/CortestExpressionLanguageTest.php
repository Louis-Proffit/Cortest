<?php

namespace App\Tests\Core\Correcteur\ExpressionLanguage;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestCompilationEnvironment;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
use App\Entity\Echelle;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use TypeError;

class CortestExpressionLanguageTest extends KernelTestCase
{

    private CortestExpressionLanguage $cortest_expression_language;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->cortest_expression_language = $container->get(CortestExpressionLanguage::class);
    }

    public function compilerProvider(): array
    {
        return [
            [["x" => "vrai1(1)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "vrai2(2)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "vrai3(3)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], SyntaxError::class],
            [["x" => "vrai4(4)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], SyntaxError::class],
            [["x" => "vrai5(5)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], SyntaxError::class],
            [["x" => "vrai1()"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], TypeError::class],
            [["x" => "vrai1(3,4)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "1", "y" => "echelle(\"x\")"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE, "y" => Echelle::TYPE_ECHELLE_COMPOSITE], null],
        ];
    }

    public function evaluerProvider(): array
    {
        return [
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vrai1(1)"], [1, 2, 3, 4, 1, 5, 6], ["x" => 1]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE, "y" => Echelle::TYPE_ECHELLE_COMPOSITE], ["x" => "1", "y" => "echelle(\"x\")"], [1, 2, 3, 4, 1, 5, 6], ["x" => 1, "y" => 1]],
        ];
    }

    /**
     * @dataProvider compilerProvider
     * @param array $expressions
     * @param array $types
     * @param string|null $exception
     */
    public function testCompiler(array $expressions, array $types, ?string $exception): void
    {
        if ($exception != null) {
            $this->expectException($exception);
        }

        foreach ($expressions as $echelle => $expression) {
            self::assertNotEmpty(
                $this->cortest_expression_language->compileCortest(
                    expression: $expression,
                    type: $types[$echelle],
                    environment: new CortestCompilationEnvironment(types: $types)
                )
            );
        }
    }

    /**
     * @dataProvider evaluerProvider
     * @param array $types
     * @param array $expressions
     * @param array $reponses
     * @param array $expected
     * @return void
     */
    public function testEvaluer(array $types, array $expressions, array $reponses, array $expected): void
    {
        $environment = new CortestEvaluationEnvironment(reponses: $reponses,
            types: $types,
            expressions: $expressions,
            cortest_expression_language: $this->cortest_expression_language);

        $result = $environment->compute_scores();
        foreach ($expected as $echelle => $score) {
            self::assertEquals(expected: $score, actual: $result[$echelle]);
        }
    }
}
