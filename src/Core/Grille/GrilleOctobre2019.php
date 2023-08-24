<?php

namespace App\Core\Grille;

/**
 * Type de grille crée en octobre 2019
 */
class GrilleOctobre2019 extends Grille
{

    const NOM = "Test psychotechnique - feuille rose";
    const NOMBRE_QUESTIONS = 640;

    public function __construct()
    {
        parent::__construct(nom: self::NOM, nombre_questions: self::NOMBRE_QUESTIONS);
    }
}