<?php

namespace App\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;

class LogEntryRepository extends \Gedmo\Loggable\Entity\Repository\LogEntryRepository
{

    const PAGE_SIZE = 100;

    public function __construct(EntityManagerInterface $em)
    {
        $logEntryRepository = $em->getRepository(LogEntry::class);
        parent::__construct($em, $logEntryRepository->getClassMetadata());
    }

    /**
     * @param int $page
     * @return LogEntry[]
     */
    public function findAllAtPage(int $page): array
    {
        return $this->createQueryBuilder('l')
            ->setFirstResult(($page - 1) * self::PAGE_SIZE)
            ->setMaxResults(self::PAGE_SIZE)
            ->orderBy("l.loggedAt", Criteria::DESC)
            ->getQuery()
            ->execute();
    }

}