<?php

namespace App\Repository;

use App\Entity\ReponseCandidat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReponseCandidat>
 *
 * @method ReponseCandidat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponseCandidat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponseCandidat[]    findAll()
 * @method ReponseCandidat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseCandidatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseCandidat::class);
    }

    public function findAllByIds(array $ids): array
    {
        return $this->createQueryBuilder("r")
            ->where("r.id IN (:ids)")
            ->setParameter("ids", $ids)
            ->getQuery()
            ->execute();
    }


}
