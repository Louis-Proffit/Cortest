<?php

namespace App\Repository;

use App\Entity\CortestUser;
use Doctrine\Bundle\DoctrineBundle\Repository\LazyServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CortestUser>
 *
 * @method CortestUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method CortestUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CortestUser[]    findAll()
 * @method CortestUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CortestUser::class);
    }
}
