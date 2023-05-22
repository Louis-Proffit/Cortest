<?php

namespace App\Core;

use App\Entity\Correcteur;
use App\Entity\Session;

class SessionCorrecteurMatcher
{
    public function match(Session $session, Correcteur $correcteur): bool
    {
        return $session->concours->id === $correcteur->concours->id;
    }
}