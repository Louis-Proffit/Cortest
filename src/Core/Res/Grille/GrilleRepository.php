<?php

namespace App\Core\Res\Grille;

use App\Core\Res\Grille\Values\GrilleBrigadierDePolice;
use App\Core\Res\Grille\Values\GrilleOctobre2019;

class GrilleRepository
{

    private array $values;

    public function __construct(
        GrilleOctobre2019       $grille_octobre_2019,
        GrilleBrigadierDePolice $grille_brigadier_de_police
    )
    {
        $this->values = [
            0 => $grille_octobre_2019,
            1 => $grille_brigadier_de_police
        ];
    }

    public function get(int $index): Grille
    {
        return $this->values[$index];
    }

    public function sample(): int
    {
        return array_keys($this->values)[0];
    }

    public function all(): array
    {
        return $this->values;
    }

    public function nomToIndex(): array
    {
        $result = [];

        foreach ($this->values as $index => $grille) {
            $result[$grille->getNom()] = $index;
        }

        return $result;
    }

}