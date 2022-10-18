<?php

namespace App\Repository;

use App\Entity\EpreuveClasseCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpreuveClasseCritere>
 *
 * @method EpreuveClasseCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpreuveClasseCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpreuveClasseCritere[]    findAll()
 * @method EpreuveClasseCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpreuveClasseCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpreuveClasseCritere::class);
    }

    public function save(EpreuveClasseCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EpreuveClasseCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EpreuveClasseCritere[] Returns an array of EpreuveClasseCritere objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EpreuveClasseCritere
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
