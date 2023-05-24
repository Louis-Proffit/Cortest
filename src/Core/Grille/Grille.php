<?php

namespace App\Core\Grille;

/**
 * Type de base pour une grille. Cela correspond à la description abstraite d'une feuille de réponse que remplit un candidat.
 */
abstract class Grille
{

    /**
     * @param string $nom
     * @param int $nombre_questions
     */
    public function __construct(public string $nom, public int $nombre_questions)
    {
    }
}