<?php

namespace App\Core\Reponses;

use App\Entity\Session;
use Exception;

class DifferentSessionException extends Exception
{

    /**
     * @param Session[] $sessions
     */
    public function __construct(
        public readonly array $sessions
    )
    {
        parent::__construct("Les réponses de candidats stockées correspondent à des session différentes : " . $this->sessionNameDisplay());
    }

    public function sessionNameDisplay(): string
    {
        $session_names = array_map(fn(Session $session) => $session->id . " | " . $session->date->format("d/m/Y") . " | " . $session->sgap->nom, $this->sessions);
        return implode(separator: " --- ", array: $session_names);
    }

}