<?php

namespace App\Core\Grille;

class Grille
{
    public string $nom;
    public int $nombre_questions;

    /**
     * @param string $nom
     * @param int $nombre_questions
     */
    public function __construct(string $nom, int $nombre_questions)
    {
        $this->nom = $nom;
        $this->nombre_questions = $nombre_questions;
    }


}