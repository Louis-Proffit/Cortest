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
}
