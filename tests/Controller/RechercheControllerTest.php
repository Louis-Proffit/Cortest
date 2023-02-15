<?php

namespace App\Tests\Controller;

use App\Controller\RechercheController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class RechercheControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testDownloadReponses()
    {
        self::markTestSkipped("TODO");
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/recherche/index");
        self::assertResponseIsSuccessful();
    }

    public function testRemoveReponseCandidat()
    {

        self::markTestSkipped("TODO");
    }

    public function testCalculerScores()
    {
        self::markTestSkipped("TODO");
    }

    public function testVider()
    {
        self::markTestSkipped("TODO");
    }
}
