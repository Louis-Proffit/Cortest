<?php

namespace App\Core\Reponses;

use App\Entity\ReponseCandidat;
use App\Entity\Session;

class CheckSingleSession
{
    /**
     * Détermine si toutes les réponses appartiennent à la même session.
     * @param ReponseCandidat[] $reponsesCandidats
     * @return Session|false
     * @throws NoReponsesCandidatException
     * @throws DifferentSessionException
     */
    public function findCommonSession(array $reponsesCandidats): Session
    {
        if (empty($reponsesCandidats)) {
            throw new NoReponsesCandidatException();
        }

        $first = $reponsesCandidats[0];
        $session = $first->session;
        $sessionId = $session->id;

        foreach ($reponsesCandidats as $reponseCandidat) {
            if ($reponseCandidat->session->id != $sessionId) {
                throw new DifferentSessionException();
            }
        }

        return $session;
    }
}