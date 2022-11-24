<?php

namespace App\Repository;

use App\Entity\EpreuveCandidat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpreuveCandidat>
 *
 * @method EpreuveCandidat|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpreuveCandidat|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpreuveCandidat[]    findAll()
 * @method EpreuveCandidat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpreuveCandidatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpreuveCandidat::class);
    }
}
