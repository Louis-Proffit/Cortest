<?php

namespace App\Tests\Controller;

use App\Repository\ConcoursRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ConcoursControllerTest extends WebTestCase
{
    use LoginTestTrait;


    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testSupprimer()
    {
        $concours = self::getContainer()->get(ConcoursRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/concours/supprimer/".$concours->id);
        self::assertResponseRedirects("/concours/index");
    }

    public function testIndex()
    {

        $this->client->request(Request::METHOD_GET, "/concours/index");
        self::assertResponseIsSuccessful();

        $text = $this->client->getCrawler()->text();

        self::assertStringContainsString("Concours", $text);
        self::assertStringContainsString("Grille", $text);
        self::assertStringContainsString("Nom", $text);
        self::assertStringContainsString("Consulter", $text);
    }

    public function testModifier()
    {
        $concours = self::getContainer()->get(ConcoursRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/concours/modifier/".$concours->id);
        self::assertResponseIsSuccessful();
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/concours/creer");
        self::assertResponseIsSuccessful();
    }
}
