<?php

namespace App\Core\Res\ProfilOuScore\Values;

use App\Core\Res\EtalonnageRow;
use App\Core\Res\ProfilOuScore\ProfilOuScore;
use App\Core\Res\Property;

class ProfilOuScoreCahierDesCharges implements ProfilOuScore
{

    private array $properties;

    public function __construct()
    {
        $this->properties = [
            new Property(nom: "collationnement", nom_php: "collationnement"),
            new Property(nom: "verbal_mot", nom_php: "verbal_mot"),
            new Property(nom: "spatial", nom_php: "spatial"),
            new Property(nom: "verbal_syntaxique", nom_php: "verbal_syntaxique"),
            new Property(nom: "raisonnement", nom_php: "raisonnement"),
            new Property(nom: "dic", nom_php: "dic"),
            new Property(nom: "anxiete", nom_php: "anxiete"),
            new Property(nom: "irritabilite", nom_php: "irritabilite"),
            new Property(nom: "impusilvite", nom_php: "impusilvite"),
            new Property(nom: "introspection", nom_php: "introspection"),
            new Property(nom: "entetement", nom_php: "entetement"),
            new Property(nom: "mefiance", nom_php: "mefiance"),
            new Property(nom: "depression", nom_php: "depression"),
            new Property(nom: "gene", nom_php: "gene"),
            new Property(nom: "manque_altruisme", nom_php: "manque_altruisme"),
            new Property(nom: "sociabilite", nom_php: "sociabilite"),
            new Property(nom: "spontaneite", nom_php: "spontaneite"),
            new Property(nom: "ascendance", nom_php: "ascendance"),
            new Property(nom: "assurance", nom_php: "assurance"),
            new Property(nom: "interet_intelletuel", nom_php: "interet_intelletuel"),
            new Property(nom: "nouveaute", nom_php: "nouveaute"),
            new Property(nom: "creativite", nom_php: "creativite"),
            new Property(nom: "rigueur", nom_php: "rigueur"),
            new Property(nom: "planification", nom_php: "planification"),
            new Property(nom: "perseverance", nom_php: "perseverance"),
            new Property(nom: "sincerite", nom_php: "sincerite"),
            new Property(nom: "obsessionalite", nom_php: "obsessionalite"),
            new Property(nom: "agressivite", nom_php: "agressivite"),
            new Property(nom: "depressivite", nom_php: "depressivite"),
            new Property(nom: "paranoidie", nom_php: "paranoidie"),
            new Property(nom: "narcissisme", nom_php: "narcissisme"),
            new Property(nom: "intolerance_a_la_frustration", nom_php: "intolerance_a_la_frustration")
        ];
    }

    function getNom(): string
    {
        return "Cahier des charges";
    }

    function getProperties(): array
    {
        return $this->properties;
    }

    public function generateEtalonnageValues(int $nombre_de_classes): array
    {
        $result = [];

        foreach ($this->properties as $property) {
            $result[$property->nom_php] = new EtalonnageRow(array_fill(0, $nombre_de_classes - 1, 0));
        }

        return $result;
    }

    function generateCorrecteurValues(): array
    {
        $result = [];
        foreach ($this->properties as $property) {
            $result[$property->nom_php] = "0";
        }
        return $result;
    }
}