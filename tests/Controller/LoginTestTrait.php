<?php

namespace App\Tests\Controller;

use App\Entity\CortestUser;
use App\Repository\CortestUserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait LoginTestTrait
{
    protected function login(KernelBrowser $client, string $role = CortestUser::ROLE_ADMINISTRATEUR): void
    {
        $client->loginUser($this->loadUser($role));
    }

    protected function loadUser(string $role): CortestUser
    {
        return self::getContainer()->get(CortestUserRepository::class)->findOneBy(["role" => $role]);
    }

}