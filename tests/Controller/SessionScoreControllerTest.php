<?php

namespace App\Tests\Controller;

use App\Repository\CorrecteurRepository;
use App\Repository\SessionRepository;
use App\Tests\LoginTestTrait;
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

    public function testConsulter()
    {
        self::markTestIncomplete("TODO, enregistrer des réponses dans la session");
        $correcteur = self::getContainer()->get(CorrecteurRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/calcul/score_brut/index/" . $correcteur->id);
        self::assertResponseIsSuccessful();
    }

    public function testForm()
    {
        self::markTestIncomplete("TODO, enregistrer des réponses dans la session");
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, "/calcul/score_brut/form");
        self::assertResponseIsSuccessful();
    }
}
