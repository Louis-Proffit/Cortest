<?php

namespace App\Core\ScoreEtalonne;

use App\Entity\Echelle;

class ScoreEtalonne
{


    /**
     * @param float[] $__array
     */
    public function __construct(
        private array $__array = []
    )
    {
    }

    public function get(Echelle $key): float
    {
        return $this->__array[$key->nom_php];
    }

    public function set(Echelle $key, float $value): void
    {
        $this->__array[$key->nom_php] = $value;
    }

    /**
     * Provides an array of $key(string) => $value(float) of each $echelle->nom_php => $score_value
     * @return float[]
     */
    public function getAll(): array
    {
        return $this->__array;
    }
}