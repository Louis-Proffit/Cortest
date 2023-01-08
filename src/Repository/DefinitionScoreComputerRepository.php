<?php

namespace App\Repository;

use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScoreComputer;
use Doctrine\Bundle\DoctrineBundle\Repository\LazyServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionScoreComputer>
 *
 * @method DefinitionScoreComputer|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefinitionScoreComputer|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefinitionScoreComputer[]    findAll()
 * @method DefinitionScoreComputer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionScoreComputerRepository extends LazyServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionScoreComputer::class);
    }

    /**
     * @param DefinitionGrille $definition_grille
     * @return DefinitionScoreComputer[]
     */
    public function findByGrilleDefinition(DefinitionGrille $definition_grille): array
    {
       return $this->createQueryBuilder('d')
            ->join("d.grille", "g", Join::WITH, "g.id = :grille_id")
            ->setParameter("grille_id", $definition_grille->id)
            ->getQuery()
            ->execute();
    }
}
