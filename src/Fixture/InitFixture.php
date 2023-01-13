<?php

namespace App\Fixture;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\Sgap;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class InitFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $sgaps = $this->getSgaps();
        foreach ($sgaps as $sgap) {
            $manager->persist($sgap);
        }

        $echelles = $this->getEchelles();
        foreach ($echelles as $echelle) {
            $manager->persist($echelle);
        }

        $manager->persist(
            new Profil(id: 0, nom: "Profil cahier des charges", echelles: new ArrayCollection($echelles))
        );

        $manager->flush();
    }

    private function getSgaps(): array
    {
        return [
            new Sgap(id: 0, index: 1, nom: "Bordeaux"),
            new Sgap(id: 0, index: 2, nom: "Dijon"),
            new Sgap(id: 0, index: 3, nom: "Lille"),
            new Sgap(id: 0, index: 4, nom: "Lyon"),
            new Sgap(id: 0, index: 5, nom: "Marseille"),
            new Sgap(id: 0, index: 6, nom: "Metz"),
            new Sgap(id: 0, index: 7, nom: "Rennes"),
            new Sgap(id: 0, index: 8, nom: "Toulouse"),
            new Sgap(id: 0, index: 9, nom: "Tours"),
            new Sgap(id: 0, index: 10, nom: "Paris"),
            new Sgap(id: 0, index: 11, nom: "Versailles"),
            new Sgap(id: 0, index: 12, nom: "Guadeloupe"),
            new Sgap(id: 0, index: 13, nom: "Guyane"),
            new Sgap(id: 0, index: 14, nom: "Martinique"),
            new Sgap(id: 0, index: 15, nom: "La Réunion"),
            new Sgap(id: 0, index: 16, nom: "Saint-Pierre et Miquelon"),
            new Sgap(id: 0, index: 17, nom: "Polynésie"),
            new Sgap(id: 0, index: 18, nom: "Nouvelle Calédonie, Wallis et Futuna"),
            new Sgap(id: 0, index: 19, nom: "Mayotte"),
            new Sgap(id: 0, index: 70, nom: "Lognes BFIE")
        ];
    }

    private function getEchelles(): array
    {
        return [
            new Echelle(id: 0, nom: "Collationnement", nom_php: "collationnement"),
            new Echelle(id: 0, nom: "Verbal mot", nom_php: "verbal_mot"),
            new Echelle(id: 0, nom: "Spatial", nom_php: "spatial"),
            new Echelle(id: 0, nom: "Verbal syntaxique", nom_php: "verbal_syntaxique"),
            new Echelle(id: 0, nom: "Raisonnement", nom_php: "raisonnement"),
            new Echelle(id: 0, nom: "Dic", nom_php: "dic"),
            new Echelle(id: 0, nom: "Anxiete", nom_php: "anxiete"),
            new Echelle(id: 0, nom: "Irritabilite", nom_php: "irritabilite"),
            new Echelle(id: 0, nom: "Impusilvite", nom_php: "impusilvite"),
            new Echelle(id: 0, nom: "Introspection", nom_php: "introspection"),
            new Echelle(id: 0, nom: "Entetement", nom_php: "entetement"),
            new Echelle(id: 0, nom: "Mefiance", nom_php: "mefiance"),
            new Echelle(id: 0, nom: "Depression", nom_php: "depression"),
            new Echelle(id: 0, nom: "Gene", nom_php: "gene"),
            new Echelle(id: 0, nom: "Manque d'altruisme", nom_php: "manque_altruisme"),
            new Echelle(id: 0, nom: "Sociabilite", nom_php: "sociabilite"),
            new Echelle(id: 0, nom: "Spontaneite", nom_php: "spontaneite"),
            new Echelle(id: 0, nom: "Ascendance", nom_php: "ascendance"),
            new Echelle(id: 0, nom: "Assurance", nom_php: "assurance"),
            new Echelle(id: 0, nom: "Interêt intelletuel", nom_php: "interet_intelletuel"),
            new Echelle(id: 0, nom: "Nouveaute", nom_php: "nouveaute"),
            new Echelle(id: 0, nom: "Creativite", nom_php: "creativite"),
            new Echelle(id: 0, nom: "Rigueur", nom_php: "rigueur"),
            new Echelle(id: 0, nom: "Planification", nom_php: "planification"),
            new Echelle(id: 0, nom: "Perseverance", nom_php: "perseverance"),
            new Echelle(id: 0, nom: "Sincerite", nom_php: "sincerite"),
            new Echelle(id: 0, nom: "Obsessionalite", nom_php: "obsessionalite"),
            new Echelle(id: 0, nom: "Agressivite", nom_php: "agressivite"),
            new Echelle(id: 0, nom: "Depressivite", nom_php: "depressivite"),
            new Echelle(id: 0, nom: "Paranoidie", nom_php: "paranoidie"),
            new Echelle(id: 0, nom: "Narcissisme", nom_php: "narcissisme"),
            new Echelle(id: 0,
                nom: "Intolerance à la frustration",
                nom_php: "intolerance_a_la_frustration"),
        ];
    }
}