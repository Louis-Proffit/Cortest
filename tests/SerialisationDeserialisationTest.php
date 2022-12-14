<?php

namespace App\Tests;

use App\Entity\CandidatReponse;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SerialisationDeserialisationTest extends KernelTestCase
{

    /** @var EntityManager $manager */
    private $manager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->manager = $container->get(ManagerRegistry::class);
    }

    public function testDeserialisation()
    {
        $all = $this->manager->getRepository(CandidatReponse::class)->findAll();

        /** @var CandidatReponse $candidat_reponse */
        foreach ($all as $candidat_reponse) {
            var_dump(unserialize($candidat_reponse->reponses));
        }

        self::assertTrue(true);
    }
}
