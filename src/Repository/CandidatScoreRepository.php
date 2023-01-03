<?php

namespace App\Repository;

use App\Entity\CandidatScore;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CandidatScore>
 *
 * @method CandidatScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatScore[]    findAll()
 * @method CandidatScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatScore::class);
    }
}
