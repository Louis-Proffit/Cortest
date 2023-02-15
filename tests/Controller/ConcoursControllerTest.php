<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ConcoursControllerTest extends WebTestCase
{
    use CrudTestTrait;

    public function testSupprimer()
    {
        self::markTestSkipped("TODO");
    }

    public function testIndex()
    {
        $this->traitTestIndex("/concours/index");
    }

    public function testModifier()
    {
        self::markTestSkipped("TODO");
    }

    public function testCreer()
    {
        self::markTestSkipped("TODO");
    }
}
