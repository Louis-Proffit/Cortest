<?php

namespace App\Repository;

use App\Entity\ReponseCandidat;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReponseCandidat>
 *
 * @method ReponseCandidat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponseCandidat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponseCandidat[]    findAll()
 * @method ReponseCandidat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseCandidatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseCandidat::class);
    }

    /**
     * @param string $nom_filter
     * @param string $prenom_filter
     * @param DateTime $date_naissance_min
     * @param DateTime $date_de_naissance_max
     * @return ReponseCandidat[]
     */
    public function filter(string   $nom_filter,
                           string   $prenom_filter,
                           DateTime $date_naissance_min,
                           DateTime $date_de_naissance_max
    ): array
    {
        $query_builder = $this->createQueryBuilder("r");
        return $query_builder
            ->where("r.nom LIKE (:nom)")
            ->andWhere("r.prenom LIKE (:prenom)")
            ->andWhere("r.date_de_naissance BEFORE (:date_de_naissance_max)")
            ->andWhere("r.date_de_naissance AFTER (:date_de_naissance_min)")
            ->setParameter("nom", $nom_filter)
            ->setParameter("prenom", $prenom_filter)
            ->setParameter("date_de_naissance_min", $date_naissance_min)
            ->setParameter("date_de_naissance_max", $date_de_naissance_max)
            ->getQuery()
            ->execute();
    }

    public function findAllByIds(array $ids): array
    {
        return $this->createQueryBuilder("r")
            ->where("r.id IN (:ids)")
            ->setParameter("ids", $ids)
            ->getQuery()
            ->execute();
    }


}
