<?php

namespace App\Core\Res\ProfilGraphique;

use App\Core\Res\ProfilOuScore\ProfilOuScore;

interface ProfilGraphique
{
    public function getTemplate(): string;

    public function getNom(): string;

    public function getProfilOuScore():ProfilOuScore;
}