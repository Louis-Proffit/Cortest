<?php

namespace App\Tests\Controller;

use App\Controller\ProfilController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProfilControllerTest extends WebTestCase
{
    use CrudTestTrait;

    public function testSupprimer()
    {
        self::markTestSkipped("TODO");
    }

    public function testCreer()
    {
        self::markTestSkipped("TODO");
    }

    public function testIndex()
    {
        $this->traitTestIndex("/profil/index");
    }
}
