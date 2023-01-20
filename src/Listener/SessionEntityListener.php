<?php

namespace App\Listener;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Session::class)]
class SessionEntityListener
{

    public function __construct(
        private readonly SessionRepository $session_repository
    )
    {
    }

    public function prePersist(Session $session, LifecycleEventArgs $event): void
    {
        $year = $session->date->format("Y");
        $numero_ordre = $this->session_repository->get_next_numero_ordre($year);
        $session->numero_ordre = $numero_ordre;
    }

}