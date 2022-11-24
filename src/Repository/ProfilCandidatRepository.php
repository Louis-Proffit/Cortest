<?php

namespace App\Repository;

use App\Entity\ProfilCandidat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfilCandidat>
 *
 * @method ProfilCandidat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfilCandidat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfilCandidat[]    findAll()
 * @method ProfilCandidat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilCandidatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilCandidat::class);
    }
}
