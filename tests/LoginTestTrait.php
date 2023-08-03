<?php

namespace App\Tests;

use App\Entity\CortestUser;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

trait LoginTestTrait
{
    use DoctrineTestTrait;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    protected function login(string $role): void
    {
        $entityManager = $this->getEntityManager();
        $user = new CortestUser(
            id: 0,
            username: "user",
            password: "pasword",
            role: $role
        );
        $entityManager->persist($user);
        $user->id = 0;
        $entityManager->flush();
        $this->client->loginUser($user);
    }
}