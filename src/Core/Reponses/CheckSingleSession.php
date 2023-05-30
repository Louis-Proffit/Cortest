<?php

namespace App\Core\Reponses;

use App\Entity\ReponseCandidat;
use App\Entity\Session;

class CheckSingleSession
{
    /**
     * Détermine si toutes les réponses appartiennent à la même session.
     * @param ReponseCandidat[] $reponsesCandidats
     * @return Session
     * @throws DifferentSessionException
     * @throws NoReponsesCandidatException
     */
    public function findCommonSession(array $reponsesCandidats): Session
    {
        if (empty($reponsesCandidats)) {
            throw new NoReponsesCandidatException();
        }

        $first = $reponsesCandidats[0];
        $session = $first->session;
        $sessionId = $session->id;

        $sessions = [$sessionId => $session];

        foreach ($reponsesCandidats as $reponseCandidat) {
            if ($reponseCandidat->session->id != $sessionId) {
                $sessions[$reponseCandidat->session->id] = $reponseCandidat->session;
            }
        }

        if (count($sessions) > 1) {
            throw new DifferentSessionException(sessions: $sessions);
        }

        return $session;
    }
}