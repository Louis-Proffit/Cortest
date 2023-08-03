<?php

namespace App\Tests\Controller;

use App\Entity\CortestUser;
use App\Tests\CortestTestTrait;
use App\Tests\LoginTestTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\assertStringContainsString;

class AdminControllerTest extends WebTestCase
{
    use CortestTestTrait;
    use LoginTestTrait;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->initClient();
        $this->login(CortestUser::ROLE_ADMINISTRATEUR);

        $entityManager = $this->getEntityManager();
        $this->persistWithoutGeneration($entityManager, new CortestUser(
            id: 1,
            username: "psychologue",
            password: "password",
            role: CortestUser::ROLE_PSYCHOLOGUE
        ));
        $entityManager->flush();
    }

    public function testIndex()
    {
        $crawler = $this->client->request(Request::METHOD_GET, "/admin/index");
        self::assertResponseIsSuccessful();

        $text = $crawler->text();

        self::assertStringContainsString("Créer", $text);
        self::assertStringContainsString("Modifier", $text);
        self::assertStringContainsString("Supprimer", $text);
    }

    /**
     * @return void
     */
    public function testModifier(): void
    {

        $this->client->request(Request::METHOD_GET, "/admin/modifier/1");
        self::assertResponseIsSuccessful();
        $html = $this->client->getCrawler()->html();
        assertStringContainsString("psychologue", $html);

        $this->client->submitForm("cortest_user[submit]");
        self::assertResponseRedirects("/admin/index");

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testCreer()
    {
        $this->client->request(Request::METHOD_GET, "/admin/creer");
        self::assertResponseIsSuccessful();

        $this->client->submitForm("creer_cortest_user[submit]", [
            "creer_cortest_user[username]" => "correcteur",
            "creer_cortest_user[role]" => CortestUser::ROLE_CORRECTEUR,
            "creer_cortest_user[password][first]" => "Password&1",
            "creer_cortest_user[password][second]" => "Password&1"
        ]);

        self::assertResponseRedirects("/admin/index");

        $crawler = $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $text = $crawler->text();
        assertStringContainsString("Création enregistrée", $text);
    }

    public function testSupprimer()
    {
        $this->client->request(Request::METHOD_GET, "/admin/supprimer/1");
        self::assertResponseRedirects("/admin/index");

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $text = $this->client->getCrawler()->text();
        assertStringContainsString("Suppression enregistrée", $text);
    }
}
