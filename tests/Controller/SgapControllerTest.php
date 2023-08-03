<?php

namespace App\Tests\Controller;

use App\Repository\SgapRepository;
use App\Tests\LoginTestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SgapControllerTest extends WebTestCase
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
        $this->client->request(Request::METHOD_GET, "/sgap/creer");
        self::assertResponseIsSuccessful();
    }

    public function testSupprimer()
    {
        $sgap = self::getContainer()->get(SgapRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/sgap/supprimer/" . $sgap->id);
        self::assertResponseRedirects("/sgap/index");
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/sgap/index");
        self::assertResponseIsSuccessful();
    }

    public function testModifier()
    {
        $sgap = self::getContainer()->get(SgapRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/sgap/modifier/" . $sgap->id);
        self::assertResponseIsSuccessful();
    }
}
