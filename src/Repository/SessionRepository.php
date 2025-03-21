<?php

namespace App\Repository;

use App\Entity\Session;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function getNextNumeroOrdre(int $year): int
    {
        $date_min = DateTime::createFromFormat("Y", $year);
        $date_max = DateTime::createFromFormat("Y", $year + 1);

        $numeros = $this->createQueryBuilder("s")
            ->select("s.numero_ordre")
            ->where("s.date BETWEEN :min AND :max")
            ->setParameter("min", $date_min)
            ->setParameter("max", $date_max)
            ->getQuery()
            ->execute();

        if (empty($numeros)) {
            return 0;
        } else {
            return max($numeros[0]);
        }
    }

    public function nullable_choices(): array
    {
        $choices = $this->choices();
        $choices["Vide"] = null;
        return $choices;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function choices(): array
    {

        $sessions = $this->findAll();

        $result = [];
        foreach ($sessions as $session) {
            $result[Session::formatLong($session)] = $session;
        }

        return $result;
    }
}
