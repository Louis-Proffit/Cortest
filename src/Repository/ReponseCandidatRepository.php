<?php

namespace App\Repository;

use App\Entity\ReponseCandidat;
use App\Form\Data\RechercheFiltre;
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
     * @param RechercheFiltre $recherche_filtre
     * @return ReponseCandidat[]
     */
    public function filtrer(RechercheFiltre $recherche_filtre
    ): array
    {
        $query_builder = $this->createQueryBuilder("r");
        $query = $query_builder
            ->where("r.nom LIKE :nom")
            ->andWhere("r.prenom LIKE :prenom")
            ->andWhere("r.date_de_naissance BETWEEN :date_de_naissance_min AND :date_de_naissance_max")
            ->setParameter("nom", "%" . $recherche_filtre->filtre_nom . "%")
            ->setParameter("prenom", "%" . $recherche_filtre->filtre_prenom . "%")
            ->setParameter("date_de_naissance_min", $recherche_filtre->filtre_date_de_naissance_min)
            ->setParameter("date_de_naissance_max", $recherche_filtre->filtre_date_de_naissance_max);

        if ($recherche_filtre->session != null) {
            $query_builder->andWhere("r.session = :session")
                ->setParameter("session", $recherche_filtre->session);
        }

        if ($recherche_filtre->niveau_scolaire != null) {
            $query_builder->andWhere("r.niveau_scolaire = :niveau_scolaire")
                ->setParameter("niveau_scolaire", $recherche_filtre->niveau_scolaire);
        }

        return $query
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
