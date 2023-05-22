<?php

namespace App\Core;

use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\Session;
use Symfony\Component\HttpFoundation\Response;

class CorrecteurEtalonnageMatcher
{
    public function match(Correcteur $correcteur, Etalonnage $etalonnage): bool
    {
        return $correcteur->profil->id === $etalonnage->profil->id;
    }
}