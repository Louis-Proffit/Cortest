<?php

namespace App\Tests\Controller;

use App\Tests\LoginTestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class HomeControllerTest extends WebTestCase
{

    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, "/");
        self::assertResponseIsSuccessful();
    }
}
