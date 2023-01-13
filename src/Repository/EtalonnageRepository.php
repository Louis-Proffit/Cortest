<?php

namespace App\Repository;

use App\Entity\Etalonnage;
use App\Entity\DefinitionScore;
use Doctrine\Bundle\DoctrineBundle\Repository\LazyServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefinitionProfilComputer>
 *
 * @method Etalonnage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etalonnage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etalonnage[]    findAll()
 * @method Etalonnage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtalonnageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etalonnage::class);
    }
}
