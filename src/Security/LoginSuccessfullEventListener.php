<?php

namespace App\Security;

use App\Core\Activite\ActiviteLogger;
use App\Entity\CortestLogEntry;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
readonly class LoginSuccessfullEventListener
{

    public function __construct(
        private ActiviteLogger $activiteLogger
    )
    {
    }

    public function __invoke(LoginSuccessEvent $loginSuccessEvent): void
    {
        $this->activiteLogger->persistAction(
            action: CortestLogEntry::ACTION_UTILISATEUR,
            object: $loginSuccessEvent->getUser(),
            message: "Connection d'un utilisateur"
        );
        $this->activiteLogger->flush();
    }

}