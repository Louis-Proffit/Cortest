<?php

namespace App\Tests;

use App\Runtime\Pdf\PdfMaker;
use PHPUnit\Framework\TestCase;

class PdfMakerTest extends TestCase
{

    public function testMake()
    {
        $instance = new PdfMaker();
        $instance->render(array('test' => 'Adolf'), "template.tex");
        self::assertTrue(true);
    }

    public function testClean()
    {
        $instance = new PdfMaker();
        $instance->clean();
        self::assertTrue(true);
    }
}
