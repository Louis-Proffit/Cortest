<?php

namespace App\Repository;

use App\Entity\DefinitionGrille;
use App\Entity\Correcteur;
use Doctrine\Bundle\DoctrineBundle\Repository\LazyServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Correcteur>
 *
 * @method Correcteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Correcteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Correcteur[]    findAll()
 * @method Correcteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorrecteurRepository extends LazyServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Correcteur::class);
    }

    /**
     * @param DefinitionGrille $definition_grille
     * @return Correcteur[]
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
