<?php

use App\Core\Entities\Grille;

class Grille1 extends Grille
{
    public string $nom;
    public string $prenom;
    public array $reponses;

    public function ifEquals(int $index, string $equalsTo, float $then): float
    {
        if ($this->reponses[$index] === $equalsTo) {
            return $then;
        } else {
            return 0;
        }
    }
}
