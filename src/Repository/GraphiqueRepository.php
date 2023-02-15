<?php

namespace App\Repository;

use App\Entity\Graphique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Graphique>
 *
 * @method Graphique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Graphique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Graphique[]    findAll()
 * @method Graphique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GraphiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Graphique::class);
    }
}
