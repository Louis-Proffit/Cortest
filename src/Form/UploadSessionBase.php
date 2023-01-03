<?php

namespace App\Form;

class UploadSessionBase
{
    public int $session_id;
    public string $contents;
    public string $nom;
    public string $prenom;
    public string $reponses;
}