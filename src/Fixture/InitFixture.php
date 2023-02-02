<?php

namespace App\Fixture;

use App\Entity\Concours;
use App\Entity\CortestUser;
use App\Entity\Echelle;
use App\Entity\NiveauScolaire;
use App\Entity\Profil;
use App\Entity\Sgap;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class InitFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        foreach ($this->sgaps() as $sgap) {
            $manager->persist($sgap);
        }

        $echelles = $this->echelles();
        foreach ($echelles as $echelle) {
            $manager->persist($echelle);
        }

        foreach ($this->concours() as $concours) {
            $manager->persist($concours);
        }

        foreach ($this->niveau_scolaire() as $niveau_scolaire) {
            $manager->persist($niveau_scolaire);
        }

        $manager->persist(
            new Profil(id: 0,
                nom: "Profil cahier des charges",
                echelles: new ArrayCollection($echelles),
                etalonnages: new ArrayCollection(),
                graphiques: new ArrayCollection())
        );

        $manager->persist(
            new CortestUser(
                id: 0,
                username: "admin",
                password: "admin",
                role: CortestUser::ROLE_ADMINISTRATEUR
            ),
        );

        $manager->persist(
            new CortestUser(
                id: 0,
                username: "psycologue",
                password: "psycologue",
                role: CortestUser::ROLE_PSYCOLOGUE
            ),
        );

        $manager->persist(
            new CortestUser(
                id: 0,
                username: "correcteur",
                password: "correcteur",
                role: CortestUser::ROLE_CORRECTEUR
            ),
        );

        $manager->flush();
    }

    private function niveau_scolaire(): array
    {
        return [
            new NiveauScolaire(id: 0, indice: 1, nom: "CEP ou niveau CEP"),
            new NiveauScolaire(id: 0, indice: 2, nom: "CAP - BEP - BEPC"),
            new NiveauScolaire(id: 0, indice: 3, nom: "Niveau BAC (1e Terminale)"),
            new NiveauScolaire(id: 0, indice: 4, nom: "BAC"),
            new NiveauScolaire(id: 0, indice: 5, nom: "BAC + 1"),
            new NiveauScolaire(id: 0, indice: 6, nom: "BAC + 2 (DEUG)"),
            new NiveauScolaire(id: 0, indice: 7, nom: "Licence ou Maîtrise"),
            new NiveauScolaire(id: 0, indice: 8, nom: "Ingénieur ou 3e cycle"),
        ];
    }

    private function concours(): array
    {
        return [
            new Concours(id: 0, nom: "Comissaire de police"),
            new Concours(id: 0, nom: "Officier (lieutenant de police)"),
            new Concours(id: 0, nom: "Sélection spécialisée - Motard - Garde de sécurité ambassade"),
            new Concours(id: 0, nom: "Gardien de la paix"),
            new Concours(id: 0, nom: "BAC - sélection spécialisée"),
            new Concours(id: 0, nom: "Tests brigadier (Entrée en formation)"),
            new Concours(id: 0, nom: "Tests brigadier (EXAPRO BIER)"),
            new Concours(id: 0, nom: "Cadet de la république"),
            new Concours(id: 0, nom: "Adjoint de sécurité"),
        ];
    }

    private function sgaps(): array
    {
        return [
            new Sgap(id: 0, indice: 1, nom: "Bordeaux"),
            new Sgap(id: 0, indice: 2, nom: "Dijon"),
            new Sgap(id: 0, indice: 3, nom: "Lille"),
            new Sgap(id: 0, indice: 4, nom: "Lyon"),
            new Sgap(id: 0, indice: 5, nom: "Marseille"),
            new Sgap(id: 0, indice: 6, nom: "Metz"),
            new Sgap(id: 0, indice: 7, nom: "Rennes"),
            new Sgap(id: 0, indice: 8, nom: "Toulouse"),
            new Sgap(id: 0, indice: 9, nom: "Tours"),
            new Sgap(id: 0, indice: 10, nom: "Paris"),
            new Sgap(id: 0, indice: 11, nom: "Versailles"),
            new Sgap(id: 0, indice: 12, nom: "Guadeloupe"),
            new Sgap(id: 0, indice: 13, nom: "Guyane"),
            new Sgap(id: 0, indice: 14, nom: "Martinique"),
            new Sgap(id: 0, indice: 15, nom: "La Réunion"),
            new Sgap(id: 0, indice: 16, nom: "Saint-Pierre et Miquelon"),
            new Sgap(id: 0, indice: 17, nom: "Polynésie"),
            new Sgap(id: 0, indice: 18, nom: "Nouvelle Calédonie, Wallis et Futuna"),
            new Sgap(id: 0, indice: 19, nom: "Mayotte"),
            new Sgap(id: 0, indice: 70, nom: "Lognes BFIE")
        ];
    }

    private function echelles(): array
    {
        return [
            new Echelle(id: 0, nom: "Collationnement", nom_php: "collationnement", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Verbal mot", nom_php: "verbal_mot", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Spatial", nom_php: "spatial", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Verbal syntaxique",
                nom_php: "verbal_syntaxique",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Raisonnement", nom_php: "raisonnement", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Dic", nom_php: "dic", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Anxiete", nom_php: "anxiete", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Irritabilite", nom_php: "irritabilite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Impusilvite", nom_php: "impusilvite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Introspection", nom_php: "introspection", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Entetement", nom_php: "entetement", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Mefiance", nom_php: "mefiance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Depression", nom_php: "depression", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Gene", nom_php: "gene", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Manque d'altruisme",
                nom_php: "manque_altruisme",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Sociabilite", nom_php: "sociabilite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Spontaneite", nom_php: "spontaneite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Ascendance", nom_php: "ascendance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Assurance", nom_php: "assurance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Interêt intelletuel",
                nom_php: "interet_intelletuel",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Nouveaute", nom_php: "nouveaute", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Creativite", nom_php: "creativite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Rigueur", nom_php: "rigueur", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Planification", nom_php: "planification", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Perseverance", nom_php: "perseverance", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Sincerite", nom_php: "sincerite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Obsessionalite", nom_php: "obsessionalite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Agressivite", nom_php: "agressivite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Depressivite", nom_php: "depressivite", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Paranoidie", nom_php: "paranoidie", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0, nom: "Narcissisme", nom_php: "narcissisme", type: Echelle::TYPE_ECHELLE_SIMPLE),
            new Echelle(id: 0,
                nom: "Intolerance à la frustration",
                nom_php: "intolerance_a_la_frustration",
                type: Echelle::TYPE_ECHELLE_SIMPLE),
        ];
    }
}