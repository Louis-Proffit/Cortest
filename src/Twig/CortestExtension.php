<?php

namespace App\Twig;

use App\Entity\EchelleGraphique;
use App\Entity\ReponseCandidat;
use App\Entity\Subtest;
use App\Repository\EchelleGraphiqueRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Fonctions Cortest ajoutées à twig, essentiellement pour le formating de certaines données
 * @see self::formatSexe()
 * @see self::formatSubtestType()
 * @see self::formatEchelleGraphiqueNom()
 * @see self::formatEchelleGraphiqueNomAffiche()
 * @see self::formatFooterType()
 */
class CortestExtension extends AbstractExtension
{

    const FULL_EIRS_NAME = ['E' => 'Externe', 'I' => 'Interne', 'R' => 'Réservé', 'S' => 'Spécial'];

    public function __construct(
        private readonly EchelleGraphiqueRepository $echelleGraphiqueRepository
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sexe', [$this, 'formatSexe']),
            new TwigFilter('eirs', [$this, 'formatEirs']),
            new TwigFilter("subtest_type", [$this, "formatSubtestType"]),
            new TwigFilter("footer_type", [$this, "formatFooterType"]),
            new TwigFilter("echelle_graphique_nom", [$this, "formatEchelleGraphiqueNom"]),
            new TwigFilter("echelle_graphique_nom_affiche", [$this, "formatEchelleGraphiqueNomAffiche"])
        ];
    }

    public function formatEchelleGraphiqueNom(int $echelle_graphique_id): string
    {
        return $this->echelleGraphiqueRepository->find($echelle_graphique_id)->echelle->nom;
    }

    public function formatEchelleGraphiqueNomAffiche(int $echelle_graphique_id): string
    {
        return $this->echelleGraphiqueRepository->find($echelle_graphique_id)->options[EchelleGraphique::OPTION_NOM_AFFICHAGE_PHP];
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

    public function formatEirs(string $eirs): string
    {
        return self::FULL_EIRS_NAME[$eirs];
    }
}