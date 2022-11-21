<?php

namespace App\Runtime\Abstracts;


abstract class Batterie
{
    private Grille $grille;

    public function __construct(Grille $grille)
    {
        $this->grille = $grille;
    }

    abstract function compute();

    /**
     * @return Grille la grille du candidat à corriger
     */
    public function getGrille(): Grille
    {
        return $this->grille;
    }
}