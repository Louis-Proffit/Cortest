<?php

namespace App\Tests\Controller;

use App\Tests\LoginTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\assertStringContainsString;

class GrilleControllerTest extends WebTestCase
{
    use LoginTestTrait;

    public function testIndex()
    {
        $client = self::createClient();
        $this->login($client);

        $client->request(Request::METHOD_GET, "/grille/index");
        self::assertResponseIsSuccessful();

        $text = $client->getCrawler()->text();
        self::assertStringContainsString("Nom", $text);
        assertStringContainsString("Nombre de questions", $text);
    }
}
