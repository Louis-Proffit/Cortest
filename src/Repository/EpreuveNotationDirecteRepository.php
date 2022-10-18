<?php

namespace App\Repository;

use App\Entity\EpreuveNotationDirecte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpreuveNotationDirecte>
 *
 * @method EpreuveNotationDirecte|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpreuveNotationDirecte|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpreuveNotationDirecte[]    findAll()
 * @method EpreuveNotationDirecte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpreuveNotationDirecteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpreuveNotationDirecte::class);
    }

    public function save(EpreuveNotationDirecte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EpreuveNotationDirecte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return EpreuveNotationDirecte[] Returns an array of EpreuveNotationDirecte objects
     */
    public function findByVersionFields($version): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.version = :version')
            ->setParameter('version', $version)
            ->orderBy('n.numQuestion', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?EpreuveNotationDirecte
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
