<?php

namespace App\Core\Grille\Values;

use App\Core\Grille\CortestGrille;
use App\Core\Grille\CortestProperty;
use App\Core\Grille\Grille;

#[CortestGrille(nom: "Grille brigadier de police", tests: [])]
class GrilleBrigadierDePolice extends Grille
{

    #[CortestProperty(nom: "Numéro de candidat")]
    public int $numero_candidat;

    #[CortestProperty(nom: "SGAP")]
    public int $sgap;

    protected function getClass(): string
    {
        return GrilleBrigadierDePolice::class;
    }

    public function fill(array $raw): void
    {
        parent::fill($raw);
        $this->numero_candidat = $raw["numero_candidat"];
        $this->sgap = $raw["sgap"];
        $this->reponses = $raw["reponses"];
    }
}