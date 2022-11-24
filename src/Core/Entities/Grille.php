<?php

namespace App\Core\Entities;

abstract class Grille
{
    private string $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }
}