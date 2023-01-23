<?php

namespace App\Tests\Core\Correcteur\ExpressionLanguage;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestExpressionEnvironment;
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

    public function testArrayAccess()
    {
        $reponses = [0, 1, 2];
        $environment = new CortestExpressionEnvironment(echelles: ["x" => "0"],
            reponses: $reponses,
            cortest_expression_language: $this->cortest_expression_language);

        self::assertEquals($reponses, $environment["reponses"]);
        self::assertEquals($reponses, ((array)$environment)["reponses"]);
    }

    public function compilerProvider(): array
    {
        return [
            ["vrai1(1)", null],
            ["vrai2(2)", null],
            ["vrai3(3)", SyntaxError::class],
            ["vrai4(4)", SyntaxError::class],
            ["vrai5(5)", SyntaxError::class],
            ["vrai1()", TypeError::class],
            ["vrai1(3,4)", null],
        ];
    }

    public function evaluerProvider(): array
    {
        return [
            [["x" => "vrai1(1)"], [1, 2, 3, 4, 1, 5, 6], ["x" => 1]],
            [["x" => "0", "y" => "echelle(\"x\")"], [], ["x" => 0, "y" => 0]],
        ];
    }

    /**
     * @dataProvider compilerProvider
     * @param string $expression
     * @param string|null $exception
     * @return void
     */
    public function testCompiler(string $expression, ?string $exception): void
    {
        self::markTestSkipped("TODO");

        if ($exception != null) {
            $this->expectException($exception);
        }

        //$this->cortest_expression_language->

        self::assertStringContainsString(CortestExpressionEnvironment::REPONSES,
            $this->cortest_expression_language->compileCortest("$expression"));
    }

    /**
     * @dataProvider evaluerProvider
     * @param array $echelles
     * @param array $reponses
     * @param array $expected
     * @return void
     */
    public function testEvaluer(array $echelles, array $reponses, array $expected): void
    {
        $environment = new CortestExpressionEnvironment(
            echelles: $echelles, reponses: $reponses, cortest_expression_language: $this->cortest_expression_language
        );

        foreach ($expected as $echelle => $score) {
            self::assertEquals($score, $environment->get_score($echelle));
        }
    }
}
