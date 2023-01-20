<?php

namespace App\Repository;

use App\Entity\NiveauScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NiveauScolaire>
 *
 * @method NiveauScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method NiveauScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method NiveauScolaire[]    findAll()
 * @method NiveauScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NiveauScolaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NiveauScolaire::class);
    }

    public function choices(): array
    {
        $items = $this->findAll();

        $result = [];

        foreach ($items as $niveau_scolaire) {
            $result[$niveau_scolaire->nom] = $niveau_scolaire;
        }

        return $result;
    }
}
