<?php

namespace App\Repository;

use App\Entity\Concours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Concours>
 *
 * @method Concours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concours[]    findAll()
 * @method Concours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concours::class);
    }

    public function nullable_choices(): array
    {
        $choices = $this->choices();
        $choices["Vide"] = null;
        return $choices;
    }

    public function choices(): array
    {

        $all_concours = $this->findAll();

        $result = [];
        foreach ($all_concours as $concours) {
            $result[$concours->intitule] = $concours;
        }

        return $result;
    }
}
