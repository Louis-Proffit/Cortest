<?php

namespace App\Tests\Controller;

use App\Controller\SessionController;
use App\Repository\SessionRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SessionControllerTest extends WebTestCase
{
    use LoginTestTrait;

    public KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/session/creer");
        self::assertResponseIsSuccessful();
    }

    public function testConsulter()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/session/consulter/" . $session->id);
        self::assertResponseIsSuccessful();
    }

    public function testSupprimer()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/session/supprimer/" . $session->id);
        self::assertResponseRedirects("/session/index");

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testCsv()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/session/consulter/" . $session->id);
        self::assertResponseIsSuccessful();
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/session/index");
        self::assertResponseIsSuccessful();
    }

    public function testModifier()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/session/modifier/" . $session->id);
        self::assertResponseIsSuccessful();
    }
}
