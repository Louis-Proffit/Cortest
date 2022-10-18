<?php

namespace App\Repository;

use App\Entity\EpreuveEchelle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpreuveEchelle>
 *
 * @method EpreuveEchelle|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpreuveEchelle|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpreuveEchelle[]    findAll()
 * @method EpreuveEchelle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpreuveEchelleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpreuveEchelle::class);
    }

    public function save(EpreuveEchelle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EpreuveEchelle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EpreuveEchelle[] Returns an array of EpreuveEchelle objects
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

//    public function findOneBySomeField($value): ?EpreuveEchelle
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
