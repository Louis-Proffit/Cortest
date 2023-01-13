<?php

namespace App\Core\Res\Grille\Values;

use App\Core\Res\Grille\CortestGrille;
use App\Core\Res\Grille\CortestProperty;
use App\Core\Res\Grille\Grille;

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