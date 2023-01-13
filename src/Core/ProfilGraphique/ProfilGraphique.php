<?php

namespace App\Core\ProfilGraphique;

interface ProfilGraphique
{
    public function getTemplate(): string;

    public function getNom(): string;

}