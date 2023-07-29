<?php

namespace App\Tests\Controller;

use App\Controller\SessionScoresEtalonnesController;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Entity\ReponseCandidat;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use App\Repository\SessionRepository;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionProfilControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testForm()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);

        $this->client->request(Request::METHOD_GET, "/calcul/profil/form/session/" . $session->id);
        self::assertResponseRedirects();

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $this->client->submitForm("Valider", ["correcteur_et_etalonnage_choice[both]" => 0]);
        self::assertResponseRedirects();

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $text = $this->client->getCrawler()->text();

        /** @var ReponseCandidat $reponses_candidat */
        foreach ($session->reponses_candidats as $reponses_candidat) {
            self::assertStringContainsString($reponses_candidat->nom, $text);
            self::assertStringContainsString($reponses_candidat->prenom, $text);
        }

        $this->client->clickLink("Exporter en .csv");
        self::assertResponseIsSuccessful();

        $csvContent = $this->client->getResponse()->getContent();

        /** @var ReponseCandidat $reponses_candidat */
        foreach ($session->reponses_candidats as $reponses_candidat) {
            self::assertStringContainsString($reponses_candidat->nom, $csvContent);
            self::assertStringContainsString($reponses_candidat->prenom, $csvContent);
        }
    }
}
