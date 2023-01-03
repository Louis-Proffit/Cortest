<?php

namespace App\Repository;

use App\Entity\DefinitionEtalonnageComputer;
use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScore;
use App\Entity\DefinitionScoreComputer;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionEtalonnageComputer>
 *
 * @method DefinitionEtalonnageComputer|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefinitionEtalonnageComputer|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefinitionEtalonnageComputer[]    findAll()
 * @method DefinitionEtalonnageComputer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionEtalonnageComputerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionEtalonnageComputer::class);
    }

    /**
    * @param DefinitionScore $definition_score
    * @return DefinitionEtalonnageComputer[]
    */
    public function findByScoreDefinition(DefinitionScore $definition_score): array
    {
        return $this->createQueryBuilder('d')
            ->join("d.score", "g", Join::WITH, "g.id = :score_id")
            ->setParameter("score_id", $definition_score->id)
            ->getQuery()
            ->execute();
    }
}
