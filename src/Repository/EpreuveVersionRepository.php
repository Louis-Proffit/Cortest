<?php

namespace App\Repository;

use App\Entity\EpreuveVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpreuveVersion>
 *
 * @method EpreuveVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpreuveVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpreuveVersion[]    findAll()
 * @method EpreuveVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpreuveVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpreuveVersion::class);
    }

    public function save(EpreuveVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EpreuveVersion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return EpreuveVersion[] Returns an array of EpreuveVersion objects
     */
    public function findByEpreuveField($value): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.epreuve = :val')
            ->setParameter('val', $value)
            ->orderBy('v.version', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeField($value): ?EpreuveVersion
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
