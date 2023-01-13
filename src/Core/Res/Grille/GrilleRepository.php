<?php

namespace App\Core\Res\Grille;

use App\Core\Res\Grille\Values\GrilleBrigadierDePolice;
use App\Core\Res\Grille\Values\GrilleOctobre2019;

class GrilleRepository
{

    private array $classes;

    public function __construct()
    {
        $this->classes = [
            GrilleOctobre2019::class,
            GrilleBrigadierDePolice::class
        ];
    }

    public function sampleClass(): string
    {
        return $this->classes[0];
    }

    public function instanceOfAll(): array
    {
        return array_map(fn($clazz) => new $clazz(), $this->classes);
    }

    public function nomToClassName(): array
    {
        $result = [];

        foreach ($this->classes as $clazz) {
            /** @var Grille $instance */
            $instance = new $clazz();
            $result[$instance->getNom()] = $clazz;
        }

        return $result;
    }

}