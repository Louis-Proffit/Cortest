<?php

namespace App\Tests\Controller;

use App\Repository\EchelleRepository;
use App\Tests\LoginTestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class EchelleControllerTest extends WebTestCase
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
        $this->client->request(Request::METHOD_GET, "/echelle/creer");
        self::assertResponseIsSuccessful();
    }

    public function testSupprimer()
    {
        $echelle = self::getContainer()->get(EchelleRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/echelle/supprimer/" . $echelle->id);
        self::assertResponseRedirects("/echelle/index");
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/echelle/index");
        self::assertResponseIsSuccessful();
    }

    public function testModifier()
    {
        $echelle = self::getContainer()->get(EchelleRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/echelle/modifier/" . $echelle->id);
        self::assertResponseIsSuccessful();
    }
}
