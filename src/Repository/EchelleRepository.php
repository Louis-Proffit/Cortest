<?php

namespace App\Repository;

use App\Entity\Echelle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Echelle>
 *
 * @method Echelle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Echelle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Echelle[]    findAll()
 * @method Echelle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchelleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Echelle::class);
    }

    /**
     * @param array $ids
     * @return Echelle[] that do not contain any of the ids in parameter
     */
    public function findByIdDifferent(array $ids): array
    {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->createQueryBuilder('e')
            ->where('e.id NOT IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('e.nom_php', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }
}
