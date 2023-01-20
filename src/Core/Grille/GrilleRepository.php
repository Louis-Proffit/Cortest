<?php

namespace App\Core\Grille;

use App\Core\Grille\Values\GrilleBrigadierDePolice;
use App\Core\Grille\Values\GrilleOctobre2019;

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

    public function classNames():array {
        return $this->classes;
    }


    public function classNameToNom(): array
    {
        $result = [];

        foreach ($this->classes as $clazz) {
            /** @var Grille $instance */
            $instance = new $clazz();
            $result[$clazz] = $instance->getNom();
        }

        return $result;
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