<?php

namespace App\Repository;

use App\Entity\EchelleGraphique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EchelleGraphique>
 *
 * @method EchelleGraphique|null find($id, $lockMode = null, $lockVersion = null)
 * @method EchelleGraphique|null findOneBy(array $criteria, array $orderBy = null)
 * @method EchelleGraphique[]    findAll()
 * @method EchelleGraphique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchelleGraphiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EchelleGraphique::class);
    }


}
