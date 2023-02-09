<?php

namespace App\Tests\Controller;

use App\Controller\LoginController;
use App\Entity\CortestUser;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\assertStringContainsString;

class LoginControllerTest extends WebTestCase
{
    use LoginTestTrait;

    public function provideSecurityCases(): Generator
    {
        yield [CortestUser::ROLE_ADMINISTRATEUR, "/admin/index", true];
        yield [CortestUser::ROLE_PSYCOLOGUE, "/admin/index", false];
        yield [CortestUser::ROLE_CORRECTEUR, "/admin/index", false];
        // TODO add cases
    }

    /**
     * @dataProvider provideSecurityCases
     * @param string $role
     * @param string $path
     * @param bool $authorized
     * @return void
     */
    public function testSecurity(string $role, string $path, bool $authorized): void
    {
        $client = self::createClient();

        $client->loginUser($this->loadUser($role));

        $client->request(Request::METHOD_GET, $path);

        if ($authorized) {
            self::assertResponseIsSuccessful();
        } else {
            self::assertResponseStatusCodeSame(403);
        }
    }

    public function provideRoles(): Generator
    {
        foreach (CortestUser::ROLES as $role) {
            yield $role => [$role];
        }
    }

    /**
     * @dataProvider provideRoles
     * @param string $role
     * @return void
     */
    public function testLogin(string $role): void
    {
        $client = self::createClient();
        $client->request(Request::METHOD_GET, "/login");
        self::assertResponseIsSuccessful();

        // dump($client->getCrawler());

        $user = $this->loadUser($role);

        $client->submitForm("Connexion", [
            "_username" => $user->username,
            "_password" => $user->username
        ]);
        self::assertResponseRedirects("http://localhost/");

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        assertStringContainsString("Cortest", $client->getCrawler()->text());
        assertStringContainsString("Se deconnecter", $client->getCrawler()->text());
    }

    /**
     * @dataProvider provideRoles
     * @param string $role
     * @return void
     */
    public function testLogout(string $role): void
    {
        $client = self::createClient();
        $this->login($client, $role);

        $client->request(Request::METHOD_GET, "/logout");
        self::assertResponseRedirects("http://localhost/");

        $client->followRedirect();
        self::assertResponseRedirects("http://localhost/login");
    }
}
