<?php

namespace App\Tests\Controller;

use App\Controller\SessionProfilController;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\SessionRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SessionScoreControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testCsv()
    {
        self::markTestSkipped("TODO");
    }

    public function testConsulter()
    {
        $correcteur = self::getContainer()->get(CorrecteurRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET,
            "/calcul/score/index/". $correcteur->id);
        self::assertResponseIsSuccessful();
    }

    public function testForm()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/calcul/score/form");
        self::assertResponseIsSuccessful();
    }
}
