<?php

namespace App\Tests\Controller;

use App\Entity\CortestUser;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait LoginTestTrait
{
    protected function login(KernelBrowser $client, string $role = CortestUser::ROLE_ADMINISTRATEUR): void
    {
        $client->loginUser($this->loadUser($role));
    }

    protected function loadUser(string $role): CortestUser
    {
        return self::getContainer()->get(UserRepository::class)->findOneBy(["role" => $role]);
    }

}