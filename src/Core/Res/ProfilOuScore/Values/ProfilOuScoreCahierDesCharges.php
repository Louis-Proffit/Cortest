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
            new Property(nom: "Collationnement", nom_php: "collationnement"),
            new Property(nom: "Verbal mot", nom_php: "verbal_mot"),
            new Property(nom: "Spatial", nom_php: "spatial"),
            new Property(nom: "Verbal syntaxique", nom_php: "verbal_syntaxique"),
            new Property(nom: "Raisonnement", nom_php: "raisonnement"),
            new Property(nom: "Dic", nom_php: "dic"),
            new Property(nom: "Anxiete", nom_php: "anxiete"),
            new Property(nom: "Irritabilite", nom_php: "irritabilite"),
            new Property(nom: "Impusilvite", nom_php: "impusilvite"),
            new Property(nom: "Introspection", nom_php: "introspection"),
            new Property(nom: "Entetement", nom_php: "entetement"),
            new Property(nom: "Mefiance", nom_php: "mefiance"),
            new Property(nom: "Depression", nom_php: "depression"),
            new Property(nom: "Gene", nom_php: "gene"),
            new Property(nom: "Manque d'altruisme", nom_php: "manque_altruisme"),
            new Property(nom: "Sociabilite", nom_php: "sociabilite"),
            new Property(nom: "Spontaneite", nom_php: "spontaneite"),
            new Property(nom: "Ascendance", nom_php: "ascendance"),
            new Property(nom: "Assurance", nom_php: "assurance"),
            new Property(nom: "Interêt intelletuel", nom_php: "interet_intelletuel"),
            new Property(nom: "Nouveaute", nom_php: "nouveaute"),
            new Property(nom: "Creativite", nom_php: "creativite"),
            new Property(nom: "Rigueur", nom_php: "rigueur"),
            new Property(nom: "Planification", nom_php: "planification"),
            new Property(nom: "Perseverance", nom_php: "perseverance"),
            new Property(nom: "Sincerite", nom_php: "sincerite"),
            new Property(nom: "Obsessionalite", nom_php: "obsessionalite"),
            new Property(nom: "Agressivite", nom_php: "agressivite"),
            new Property(nom: "Depressivite", nom_php: "depressivite"),
            new Property(nom: "Paranoidie", nom_php: "paranoidie"),
            new Property(nom: "Narcissisme", nom_php: "narcissisme"),
            new Property(nom: "Intolerance à la frustration", nom_php: "intolerance_a_la_frustration")
        ];
    }

    function getNom(): string
    {
        return "Profil cahier des charges";
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