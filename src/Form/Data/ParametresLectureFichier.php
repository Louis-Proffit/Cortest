<?php

namespace App\Form\Data;

use App\Entity\Session;

class ParametresLectureFichier
{
    public Session $session;
    public string $contents;
    public string $nom;
    public string $prenom;
    public string $reponses;
}