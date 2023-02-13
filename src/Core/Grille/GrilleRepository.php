<?php

namespace App\Core\Grille;

use App\Core\Grille\Values\GrilleBrigadierDePolice;
use App\Core\Grille\Values\GrilleOctobre2019;

class GrilleRepository
{

    const CLASSES =  [
        GrilleOctobre2019::class,
        GrilleBrigadierDePolice::class
    ];


    public function instanceOfAll(): array
    {
        return array_map(fn($clazz) => new $clazz(), self::CLASSES);
    }


    public function classNameToNom(): array
    {
        $result = [];

        foreach (self::CLASSES as $clazz) {
            /** @var Grille $instance */
            $instance = new $clazz();
            $result[$clazz] = $instance->getNom();
        }

        return $result;
    }
    public function nomToClassName(): array
    {
        $result = [];

        foreach (self::CLASSES as $clazz) {
            /** @var Grille $instance */
            $instance = new $clazz();
            $result[$instance->getNom()] = $clazz;
        }

        return $result;
    }

}