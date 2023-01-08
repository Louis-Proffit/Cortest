<?php

namespace App\Repository;

use App\Entity\DefinitionProfilComputer;
use App\Entity\DefinitionScore;
use Doctrine\Bundle\DoctrineBundle\Repository\LazyServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionProfilComputer>
 *
 * @method DefinitionProfilComputer|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefinitionProfilComputer|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefinitionProfilComputer[]    findAll()
 * @method DefinitionProfilComputer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionProfilComputerRepository extends LazyServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionProfilComputer::class);
    }

    /**
     * @param DefinitionScore $definition_score
     * @return DefinitionProfilComputer[]
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
