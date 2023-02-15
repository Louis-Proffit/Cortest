<?php

namespace App\Tests\Controller;

use App\Controller\LectureController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class LectureControllerTest extends WebTestCase
{
    use LoginTestTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->login($this->client);
    }

    public function testLectureHome()
    {
        $this->client->request(Request::METHOD_GET, "/lecture/index");
        self::assertResponseIsSuccessful();
    }

    public function testFichier()
    {

        $this->client->request(Request::METHOD_GET, "/lecture/fichier");
        self::assertResponseIsSuccessful();
    }

    public function testScanner()
    {
        $this->client->request(Request::METHOD_GET, "/lecture/scanner");
        self::assertResponseIsSuccessful();

    }

    public function testForm()
    {

        $this->client->request(Request::METHOD_GET, "/lecture/form");
        self::assertResponseIsSuccessful();
    }
}
