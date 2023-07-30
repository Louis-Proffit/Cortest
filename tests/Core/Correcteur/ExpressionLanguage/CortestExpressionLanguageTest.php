<?php

namespace App\Tests\Core\Correcteur\ExpressionLanguage;

use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\ScoreBrut\ExpressionLanguage\Environment\CortestCompilationEnvironment;
use App\Core\ScoreBrut\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
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
            [["x" => "fauxA(1)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "fauxB(2)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "fauxC(3)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "fauxD(4)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "fauxE(5)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "fauxA()"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], TypeError::class],
            [["x" => "fauxA(3,4)"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE], null],
            [["x" => "1", "y" => "echelle(\"x\")"], ["x" => Echelle::TYPE_ECHELLE_SIMPLE, "y" => Echelle::TYPE_ECHELLE_COMPOSITE], null],
        ];
    }

    public function evaluerProvider(): array
    {
        return [
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiA(1)"], [1, 2, 3, 4, 5, 0], ["x" => 1]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiB(2)"], [1, 2, 3, 4, 5, 0], ["x" => 1]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiC(3)"], [1, 2, 3, 4, 5, 0], ["x" => 1]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiD(4)"], [1, 2, 3, 4, 5, 0], ["x" => 1]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiE(5)"], [1, 2, 3, 4, 5, 0], ["x" => 1]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiA(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiB(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiC(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiD(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "vraiE(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxA(1)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxB(2)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxC(3)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxD(4)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxE(5)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxA(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxB(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxC(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxD(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "fauxE(6)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "score43210(1)"], [1, 2, 3, 4, 5, 0], ["x" => 4]],
            [["x" => Echelle::TYPE_ECHELLE_SIMPLE], ["x" => "score01234(1)"], [1, 2, 3, 4, 5, 0], ["x" => 0]],
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
                $this->cortest_expression_language->cortestCompile(
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
            echelles: $types,
            expressions: $expressions,
            cortestExpressionLanguage: $this->cortest_expression_language);

        $result = $environment->computeScores();
        foreach ($expected as $echelle => $score) {
            self::assertEquals(expected: $score, actual: $result[$echelle]);
        }
    }
}
