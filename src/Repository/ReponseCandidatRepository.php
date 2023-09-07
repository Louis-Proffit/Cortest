<?php

namespace App\Repository;

use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Form\Data\ParametresRecherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\QueryException;
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
     * @throws QueryException
     */
    public function findAllIdsFromParameters(ParametresRecherche $rechercheParameters): array
    {
        $queryBuilder = $this->createQueryBuilder("r", "r.id");
        $query = $queryBuilder
            ->select("r.id")
            ->innerJoin("r.session", "s")
            ->where("r.nom LIKE :nom")
            ->andWhere("r.prenom LIKE :prenom")
            ->andWhere("r.date_de_naissance BETWEEN :date_de_naissance_min AND :date_de_naissance_max")
            ->setFirstResult(ParametresRecherche::PAGE_SIZE * $rechercheParameters->page)
            ->setMaxResults(ParametresRecherche::PAGE_SIZE)
            ->setParameter("nom", "%" . $rechercheParameters->filtreNom . "%")
            ->setParameter("prenom", "%" . $rechercheParameters->filtrePrenom . "%")
            ->setParameter("date_de_naissance_min", $rechercheParameters->filtreDateDeNaissanceMin)
            ->setParameter("date_de_naissance_max", $rechercheParameters->filtreDateDeNaissanceMax);

        if ($rechercheParameters->dateSession != null) {
            $debut = $rechercheParameters->dateSession;
            $debut = $debut->setTime(0, 0);
            $fin = clone $debut;
            $fin->setTime(13, 59, 59);
            $query->andWhere("s.date BETWEEN :dateSessionDebut AND :dateSessionFin")
                ->setParameter("dateSessionDebut", $debut)
                ->setParameter("dateSessionFin", $fin);
        }

        if ($rechercheParameters->session != null) {
            $query->andWhere("r.session = :session")
                ->setParameter("session", $rechercheParameters->session);
        }

        if ($rechercheParameters->niveauScolaire != null) {
            $query->andWhere("r.niveau_scolaire = :niveau_scolaire")
                ->setParameter("niveau_scolaire", $rechercheParameters->niveauScolaire);
        }

        return $query->getQuery()->execute();
    }

    /**
     * @param array $ids
     * @return ReponseCandidat[]
     */
    public function findAllByIdsIndexById(array $ids): array
    {
        return $this->createQueryBuilder("r", "r.id")
            ->where("r.id IN (:ids)")
            ->setParameter("ids", $ids)
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
