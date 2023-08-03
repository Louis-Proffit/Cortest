<?php

namespace App\Tests\Controller;

use App\Entity\Concours;
use App\Entity\CortestUser;
use App\Tests\CortestTestTrait;
use App\Tests\DoctrineTestTrait;
use App\Tests\LoginTestTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ConcoursControllerTest extends WebTestCase
{
    use CortestTestTrait;
    use LoginTestTrait;
    use DoctrineTestTrait;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->initClient();
        $this->login(CortestUser::ROLE_PSYCHOLOGUE);
        $entityManager = $this->getEntityManager();
        $this->persistWithoutGeneration($entityManager, new Concours(
            id: 2,
            nom: "Concours 2",
            type_concours: 2,
            tests: new ArrayCollection()
        ));
        $this->persistWithoutGeneration($entityManager, new Concours(
            id: 1,
            nom: "Concours 1",
            type_concours: 1,
            tests: new ArrayCollection()
        ));
        $entityManager->flush();
    }

    public function testSupprimer()
    {
        $this->client->request(Request::METHOD_GET, "/concours/supprimer/1");
        self::assertResponseRedirects("/concours/index");
    }

    public function testIndex()
    {

        $this->client->request(Request::METHOD_GET, "/concours/index");
        self::assertResponseIsSuccessful();

        $text = $this->client->getCrawler()->text();

        self::assertStringContainsString("Concours", $text);
        self::assertStringContainsString("Type de concours", $text);
        self::assertStringContainsString("Grille", $text);
        self::assertStringContainsString("Nom", $text);
        self::assertStringContainsString("Créer", $text);
        self::assertStringContainsString("Supprimer", $text);
    }

    public function testModifier()
    {
        $this->client->request(Request::METHOD_GET, "/concours/modifier/1");
        self::assertResponseIsSuccessful();
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/concours/creer");
        self::assertResponseIsSuccessful();

        $champs = ["concours[intitule]" => "concours", "concours[type_concours]" => 3];

        $this->client->submitForm("Créer", $champs);
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
    }
}
