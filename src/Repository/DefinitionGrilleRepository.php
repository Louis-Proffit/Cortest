<?php

namespace App\Repository;

use App\Entity\DefinitionGrille;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionGrille>
 *
 * @method DefinitionGrille|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefinitionGrille|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefinitionGrille[]    findAll()
 * @method DefinitionGrille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefinitionGrilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefinitionGrille::class);
    }
}
