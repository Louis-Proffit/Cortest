<?php

namespace App\Tests\Controller;

use App\Controller\ReponseCandidatController;
use App\Repository\ReponseCandidatRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ReponseCandidatControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    /**
     * @throws \Exception
     */
    public function testSupprimer()
    {
        $reponse_candidat = self::getContainer()->get(ReponseCandidatRepository::class)->findOneBy([]);
        $session_id = $reponse_candidat->session->id;
        $this->client->request(Request::METHOD_GET, "/reponse-candidat/supprimer/" . $reponse_candidat->id);
        self::assertResponseRedirects("/session/consulter/" . $session_id);
    }
}
