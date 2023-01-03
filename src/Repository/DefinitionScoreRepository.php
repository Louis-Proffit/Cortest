<?php

namespace App\Repository;

use App\Entity\DefinitionScore;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionScore>
 *
 * @method DefinitionScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefinitionScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefinitionScore[]    findAll()
 * @method DefinitionScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionScore::class);
    }
}
