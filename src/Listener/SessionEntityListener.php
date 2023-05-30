<?php

namespace App\Listener;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Configure le numéro d'ordre d'une session au moment de la persister.
 * Utilise pour cela {@link SessionRepository::getNextNumeroOrdre()}
 */
#[AsEntityListener(event: Events::prePersist, method: "prePersist", entity: Session::class)]
class SessionEntityListener
{

    public function __construct(
        private readonly SessionRepository $sessionRepository
    )
    {
    }

    public function prePersist(Session $session, LifecycleEventArgs $event): void
    {
        $year = $session->date->format("Y");
        $numeroOrdre = $this->sessionRepository->getNextNumeroOrdre($year);
        $session->numero_ordre = $numeroOrdre;
    }

}