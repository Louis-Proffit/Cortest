<?php

namespace App\Repository;

use App\Entity\ReponseCandidat;
use App\Form\Data\RechercheParameters;
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
     * @param RechercheParameters $rechercheParameters
     * @return ReponseCandidat[]
     */
    public function findAllFromParameters(RechercheParameters $rechercheParameters
    ): array
    {
        $query_builder = $this->createQueryBuilder("r");
        $query = $query_builder
            ->where("r.nom LIKE :nom")
            ->andWhere("r.prenom LIKE :prenom")
            ->andWhere("r.date_de_naissance BETWEEN :date_de_naissance_min AND :date_de_naissance_max")
            ->setFirstResult(RechercheParameters::PAGE_SIZE * $rechercheParameters->page)
            ->setMaxResults(RechercheParameters::PAGE_SIZE)
            ->setParameter("nom", "%" . $rechercheParameters->filtreNom . "%")
            ->setParameter("prenom", "%" . $rechercheParameters->filtrePrenom . "%")
            ->setParameter("date_de_naissance_min", $rechercheParameters->filtreDateDeNaissanceMin)
            ->setParameter("date_de_naissance_max", $rechercheParameters->filtreDateDeNaissanceMax);

        if ($rechercheParameters->session != null) {
            $query_builder->andWhere("r.session = :session")
                ->setParameter("session", $rechercheParameters->session);
        }

        if ($rechercheParameters->niveauScolaire != null) {
            $query_builder->andWhere("r.niveau_scolaire = :niveau_scolaire")
                ->setParameter("niveau_scolaire", $rechercheParameters->niveauScolaire);
        }

        return $query
            ->getQuery()
            ->execute();
    }

    /**
     * @param array $ids
     * @return ReponseCandidat[]
     */
    public function findAllByIds(array $ids): array
    {
        return $this->createQueryBuilder("r")
            ->where("r.id IN (:ids)")
            ->setParameter("ids", $ids)
            ->getQuery()
            ->execute();
    }


}
