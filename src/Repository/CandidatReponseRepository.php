<?php

namespace App\Repository;

use App\Entity\CandidatReponse;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CandidatReponse>
 *
 * @method CandidatReponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatReponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatReponse[]    findAll()
 * @method CandidatReponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatReponse::class);
    }


}
