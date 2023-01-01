<?php

namespace App\Entity;

class UploadSessionBase
{
    public int $session_id;
    public string $contents;
    public string $nom;
    public string $prenom;
    public string $reponses;
}