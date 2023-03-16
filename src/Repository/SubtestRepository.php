<?php

namespace App\Repository;

use App\Entity\Subtest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subtest>
 *
 * @method Subtest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subtest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subtest[]    findAll()
 * @method Subtest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubtestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subtest::class);
    }
}
