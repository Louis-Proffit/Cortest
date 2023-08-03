<?php

namespace App\Tests\Security;

use App\Entity\CortestUser;
use App\Repository\CortestUserRepository;
use App\Security\CheckAdministrateurCount;
use App\Tests\DoctrineTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckAdministrateurCountTest extends KernelTestCase
{
    use DoctrineTestTrait;

    private CortestUserRepository $userRepository;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist(new CortestUser(
            id: 0,
            username: "username",
            password: "password",
            role: CortestUser::ROLE_ADMINISTRATEUR
        ));
        $entityManager->flush();

        $this->userRepository = self::getContainer()->get(CortestUserRepository::class);

    }

    public function testOneAdministrateur()
    {

        /** @var CheckAdministrateurCount $checkAdministrateurCount */
        $checkAdministrateurCount = self::getContainer()->get(CheckAdministrateurCount::class);

        $administrateurCount = $this->userRepository->count(["role" => CortestUser::ROLE_ADMINISTRATEUR]);
        self::assertEquals(1, $administrateurCount);

        self::assertTrue($checkAdministrateurCount->atLeastOneAdministrateur());
    }
}
