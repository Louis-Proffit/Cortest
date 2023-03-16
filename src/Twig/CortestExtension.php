<?php

namespace App\Twig;

use App\Entity\EchelleGraphique;
use App\Entity\ReponseCandidat;
use App\Entity\Subtest;
use App\Repository\EchelleGraphiqueRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CortestExtension extends AbstractExtension
{


    public function __construct(
        private readonly EchelleGraphiqueRepository $echelle_graphique_repository
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sexe', [$this, 'formatSexe']),
            new TwigFilter("subtest_type", [$this, "formatSubtestType"]),
            new TwigFilter("footer_type", [$this, "formatFooterType"]),
            new TwigFilter("echelle_graphique_nom", [$this, "formatEchelleGraphiqueNom"]),
            new TwigFilter("echelle_graphique_nom_affiche", [$this, "formatEchelleGraphiqueNomAffiche"])
        ];
    }

    public function formatEchelleGraphiqueNom(int $echelle_graphique_id): string
    {
        return $this->echelle_graphique_repository->find($echelle_graphique_id)->echelle->nom;
    }

    public function formatEchelleGraphiqueNomAffiche(int $echelle_graphique_id): string
    {
        return $this->echelle_graphique_repository->find($echelle_graphique_id)->options[EchelleGraphique::OPTION_NOM_AFFICHAGE_PHP];
    }

    public function formatFooterType(int $footer_type): string
    {
        return array_flip(Subtest::TYPES_FOOTER_CHOICES)[$footer_type];
    }

    public function formatSubtestType(int $subtest_index): string
    {
        return array_flip(Subtest::TYPES_SUBTEST_CHOICES)[$subtest_index];
    }

    public function formatSexe(int $sexe_index): string
    {
        if ($sexe_index == ReponseCandidat::INDEX_HOMME) {
            return "Homme";
        } else {
            return "Femme";
        }
    }
}