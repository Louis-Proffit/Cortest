<?php

namespace App\Core\Res\Grille;

use App\Core\Res\Property;
use Exception;
use ReflectionClass;

abstract class Grille
{
    #[CortestProperty(nom: "Réponses")]
    public array $reponses;

    protected abstract function getClass(): string;

    public function fill(array $raw): void
    {
        $this->reponses = $raw["reponses"];
    }

    public function getNom(): string
    {
        $reflectiveClass = new ReflectionClass($this->getClass());

        $attributes = $reflectiveClass->getAttributes(CortestGrille::class);
        if (empty($attributes)) {
            throw new Exception("Grille class misses " . CortestGrille::class . " annotation");
        }

        if (count($attributes) > 1) {
            throw new Exception("Grille class has too many " . CortestGrille::class . " annotations");
        }

        $attribute = $attributes[0];

        return $attribute->getArguments()["nom"];
    }

    public function getProperties(): array
    {
        $reflectiveClass = new ReflectionClass($this->getClass());

        $result = [];

        $properties = $reflectiveClass->getProperties();

        foreach ($properties as $property) {

            $attributes = $property->getAttributes(CortestProperty::class);

            if (!empty($attributes)) {

                if (count($attributes) > 1) {
                    throw new Exception("Too many " . CortestProperty::class . " attributes");
                }

                $attribute = $attributes[0];

                $result[] = new Property(
                    nom: $attribute->getArguments()["nom"],
                    nom_php: $property->getName()
                );
            }
        }

        return $result;
    }


}