<?php

namespace App\Repository;

use App\Entity\CortestLogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Loggable\Entity\LogEntry;

/**
 * @extends ServiceEntityRepository CortestLogEntry>
 *
 * @method CortestLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method CortestLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method CortestLogEntry[]    findAll()
 * @method CortestLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CortestLogEntryRepository extends ServiceEntityRepository
{

    const PAGE_SIZE = 100;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CortestLogEntry::class);
    }


    /**
     * @param int $page
     * @return CortestLogEntry[]
     */
    public function findAllAtPage(int $page): array
    {
        return $this->createQueryBuilder('l')
            ->setFirstResult(($page - 1) * self::PAGE_SIZE)
            ->setMaxResults(self::PAGE_SIZE)
            ->orderBy("l.logged_at", Criteria::DESC)
            ->getQuery()
            ->execute();
    }
}
