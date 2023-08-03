<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

trait DoctrineTestTrait
{

    /**
     * @throws Exception
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }

    protected function persistWithoutGeneration(EntityManagerInterface $entityManager, mixed $entity): void
    {
        $oldId = $entity->id;
        $entityManager->persist($entity);
        $entity->id = $oldId;
    }

}