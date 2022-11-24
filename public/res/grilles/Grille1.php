<?php

use App\Core\Entities\Grille;

class Grille1 extends Grille
{
    public string $nom;
    public string $prenom;
    public array $reponses;

    public function __construct(string $raw)
    {
        parent::__construct($raw);
        $this->nom = "Louis"; // TODO
        $this->prenom = "Proffit"; // TODO
        $this->reponses = array(0, 1, 2, 3); // TODO
    }

    public function ifEquals(int $index, string $equalsTo, float $then): float
    {
        if ($this->reponses[$index] === $equalsTo) {
            return $then;
        } else {
            return 0;
        }
    }


}
