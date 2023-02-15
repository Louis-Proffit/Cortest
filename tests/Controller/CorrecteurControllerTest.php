<?php

namespace App\Tests\Controller;

use App\Controller\CorrecteurController;
use App\Repository\CorrecteurRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CorrecteurControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/correcteur/creer");
        self::assertResponseIsSuccessful();
    }

    public function testSupprimer()
    {
        $correcteur = self::getContainer()->get(CorrecteurRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/correcteur/supprimer/" . $correcteur->id);
        self::assertResponseRedirects("/correcteur/index");
    }

    public function testModifier()
    {
        $correcteur = self::getContainer()->get(CorrecteurRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/correcteur/modifier/" . $correcteur->id);
        self::assertResponseIsSuccessful();
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/correcteur/index");
        self::assertResponseIsSuccessful();

        $text = $this->client->getCrawler()->text();

        self::assertStringContainsString("Correcteur", $text);
        self::assertStringContainsString("Nom", $text);
        self::assertStringContainsString("Concours", $text);
        self::assertStringContainsString("Profil", $text);
        self::assertStringContainsString("Consulter", $text);
    }

    public function testConsulter()
    {
        $correcteur = self::getContainer()->get(CorrecteurRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/correcteur/consulter/" . $correcteur->id);
        self::assertResponseIsSuccessful();
    }
}
