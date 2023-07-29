<?php

namespace App\Tests\Security;

use App\Entity\CortestUser;
use App\Repository\CortestUserRepository;
use App\Security\CheckAdministrateurCount;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckAdministrateurCountTest extends KernelTestCase
{

    public function testOneAdministrateur()
    {

        /** @var CortestUserRepository $userRepository */
        $userRepository = self::getContainer()->get(CortestUserRepository::class);
        /** @var CheckAdministrateurCount $checkAdministrateurCount */
        $checkAdministrateurCount = self::getContainer()->get(CheckAdministrateurCount::class);

        self::assertEquals(1, $userRepository->count(["role" => CortestUser::ROLE_ADMINISTRATEUR]));

        self::assertTrue($checkAdministrateurCount->atLeastOneAdministrateur());
    }
}
