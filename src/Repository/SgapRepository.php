<?php

namespace App\Repository;

use App\Entity\Sgap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sgap>
 *
 * @method Sgap|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sgap|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sgap[]    findAll()
 * @method Sgap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SgapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sgap::class);
    }

    public function choices(): array
    {
        $items = $this->findAll();

        $result = [];

        foreach ($items as $sgap) {
            $result[$sgap->nom] = $sgap;
        }

        return $result;
    }
}
