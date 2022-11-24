<?php

namespace App\Repository;

use App\Entity\BatterieProfil;
use App\Entity\EpreuveCandidat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/*
 * @extends ServiceEntityRepository<BatterieProfil>
 *
 * @method BatterieProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method BatterieProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method BatterieProfil[]    findAll()
 * @method BatterieProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BatterieProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BatterieProfil::class);
    }
}
