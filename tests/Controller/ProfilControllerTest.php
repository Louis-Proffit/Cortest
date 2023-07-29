<?php

namespace App\Tests\Controller;

use App\Controller\ProfilController;
use App\Repository\StructureRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProfilControllerTest extends WebTestCase
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
        $profil = self::getContainer()->get(StructureRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/profil/supprimer/".$profil->id);
        self::assertResponseRedirects("/profil/index");
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/profil/creer");
        self::assertResponseIsSuccessful();
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/profil/index");
        self::assertResponseIsSuccessful();
    }
}
