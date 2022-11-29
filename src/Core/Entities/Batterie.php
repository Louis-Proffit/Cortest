<?php

namespace App\Core\Entities;


abstract class Batterie
{

    abstract function compute($grille): Profil;
}