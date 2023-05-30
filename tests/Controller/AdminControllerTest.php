<?php

namespace App\Tests\Controller;

use App\Controller\AdminController;
use App\Entity\CortestUser;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\assertStringContainsString;

class AdminControllerTest extends WebTestCase
{
    use LoginTestTrait;

    public function testIndex()
    {
        $client = self::createClient();
        $this->login($client);

        $client->request(Request::METHOD_GET, "/admin/index");
        self::assertResponseIsSuccessful();
        self::assertStringContainsString("Créer", $client->getCrawler()->text());
        self::assertStringContainsString("Modifier", $client->getCrawler()->text());
        self::assertStringContainsString("Supprimer", $client->getCrawler()->text());
    }

    /**
     * @return void
     */
    public function testModifier(): void
    {
        $client = self::createClient();
        $this->login($client);

        $psycologue = $this->loadUser(CortestUser::ROLE_PSYCHOLOGUE);

        $client->request(Request::METHOD_GET, "/admin/modifier/" . $psycologue->id);
        self::assertResponseIsSuccessful();
        $html = $client->getCrawler()->html();
        assertStringContainsString($psycologue->username, $html);

        $client->submitForm("cortest_user[submit]");
        self::assertResponseRedirects("/admin/index");

        $client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testCreer()
    {
        $client = self::createClient();
        $this->login($client);

        $client->request(Request::METHOD_GET, "/admin/creer");
        self::assertResponseIsSuccessful();

        $client->submitForm("creer_cortest_user[submit]", [
            "creer_cortest_user[username]" => "x",
            "creer_cortest_user[role]" => CortestUser::ROLE_CORRECTEUR,
            "creer_cortest_user[password][first]" => "xxxxxxxxxxxx",
            "creer_cortest_user[password][second]" => "xxxxxxxxxxxx"
        ]);
        self::assertResponseRedirects("/admin/index");

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        assertStringContainsString("Création enregistrée", $client->getCrawler()->text());
    }

    public function testSupprimer()
    {
        $client = self::createClient();
        $this->login($client);

        $psycologue = $this->loadUser(CortestUser::ROLE_PSYCHOLOGUE);

        $client->request(Request::METHOD_GET, "/admin/supprimer/" . $psycologue->id);
        self::assertResponseRedirects("/admin/index");

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        assertStringContainsString("Suppression enregistrée", $client->getCrawler()->text());
    }
}
