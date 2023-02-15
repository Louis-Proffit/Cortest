<?php

namespace App\Tests\Controller;

use App\Controller\EchelleController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class EchelleControllerTest extends WebTestCase
{
    use CrudTestTrait;

    public function testCreer()
    {
        self::markTestSkipped("TODO");
    }

    public function testSupprimer()
    {
        self::markTestSkipped("TODO");
    }

    public function testIndex()
    {
        $this->traitTestIndex("/echelle/index");
    }

    public function testModifier()
    {
        self::markTestSkipped("TODO");
    }
}
