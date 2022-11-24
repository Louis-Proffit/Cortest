<?php

namespace App\Core\Entities;


abstract class FonctionProfil
{

    abstract function compute($reponses): Profil;
}