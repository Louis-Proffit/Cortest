<?php

namespace App\Tests\Controller;

use App\Controller\GraphiqueController;
use App\Repository\GraphiqueRepository;
use App\Repository\GrilleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GraphiqueControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/graphique/index");
        self::assertResponseIsSuccessful();
    }

    public function testConsulter()
    {
        $graphique = self::getContainer()->get(GraphiqueRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/graphique/consulter/" . $graphique->id);
        self::assertResponseIsSuccessful();
    }

    public function testModifier()
    {

        $graphique = self::getContainer()->get(GraphiqueRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/graphique/modifier/" . $graphique->id);
        self::assertResponseIsSuccessful();
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/graphique/index");
        self::assertResponseIsSuccessful();
    }

    public function testSupprimer()
    {

        $graphique = self::getContainer()->get(GraphiqueRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/graphique/supprimer/" . $graphique->id);
        self::assertResponseRedirects("/graphique/index");
    }
}
