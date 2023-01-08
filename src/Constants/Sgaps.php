<?php

namespace App\Constants;

class Sgaps
{

    private array $sgaps;

    /**
     * @param array $sgaps
     */
    public function __construct(array $sgaps)
    {
        $this->sgaps = $sgaps;
    }

    public function nom(int $index): string
    {
        return array_key_exists($index, $this->sgaps) ? $this->sgaps[$index] : "SGAP indéfini";
    }

    public function nomToIndex():array {
        return array_flip($this->sgaps);
    }

}