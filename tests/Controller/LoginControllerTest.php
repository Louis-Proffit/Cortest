<?php

namespace App\Tests\Controller;

use App\Entity\CortestUser;
use App\Tests\LoginTestTrait;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\assertStringContainsString;

class LoginControllerTest extends WebTestCase
{
    use LoginTestTrait;

    public function provideSecurityCases(): Generator
    {
        yield [CortestUser::ROLE_ADMINISTRATEUR, "/admin/index", true];
        yield [CortestUser::ROLE_PSYCHOLOGUE, "/admin/index", false];
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

        $this->login($client, $role);

        $client->request(Request::METHOD_GET, $path);

        if ($authorized) {
            self::assertResponseIsSuccessful();
        } else {
            self::assertResponseRedirects("/login");
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
        $client->request(Request::METHOD_GET, "/");
        self::assertResponseRedirects();

        $client->followRedirect();
        self::assertResponseIsSuccessful();

        $user = $this->loadUser($role);

        $client->submitForm("Connexion", [
            "_username" => $user->username,
            "_password" => $user->password
        ]);

        self::assertResponseRedirects("http://localhost/");

        $client->followRedirect();
        self::assertResponseIsSuccessful();

        $text = $client->getCrawler()->text();
        assertStringContainsString("Cortest", $text);
        assertStringContainsString("Se deconnecter", $text);
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
