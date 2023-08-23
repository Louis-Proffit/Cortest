<?php

namespace App\Security;

use App\Core\Activite\ActiviteLogger;
use App\Entity\CortestLogEntry;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LogoutEvent::class)]
readonly class LogoutEventListener
{

    public function __construct(
        private ActiviteLogger $activiteLogger
    )
    {
    }

    public function __invoke(LogoutEvent $logoutEvent): void
    {
        $this->activiteLogger->persistAction(
            action: CortestLogEntry::ACTION_UTILISATEUR,
            object: $logoutEvent->getToken()->getUser(),
            message: "Déconnection d'un utilisateur"
        );
        $this->activiteLogger->flush();
    }

}