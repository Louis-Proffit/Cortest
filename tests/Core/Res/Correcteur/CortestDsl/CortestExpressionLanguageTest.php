<?php

namespace App\Tests\Core\Res\Correcteur\CortestDsl;

use App\Core\Res\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use PHPUnit\Framework\TestCase;

class CortestExpressionLanguageTest extends TestCase
{

    function testValueFunction()
    {
        $el = new CortestExpressionLanguage();

        self::assertEquals(1, $el->evaluate("vrai1(3)", ["reponses" => str_split("13024")]));
        self::assertEquals(0, $el->evaluate("vrai1(2)", ["reponses" => str_split("13024")]));
    }

}
