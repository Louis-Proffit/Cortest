<?php

namespace App\Tests\Controller;

use App\Entity\CortestUser;
use Symfony\Component\HttpFoundation\Request;

trait CrudTestTrait
{

    use LoginTestTrait;

    public function traitTestIndex(string $path, string $role = CortestUser::ROLE_ADMINISTRATEUR): void
    {
        $client = self::createClient();

        $this->login($client, $role);

        $client->request(Request::METHOD_GET, $path);
        self::assertResponseIsSuccessful();
    }
}