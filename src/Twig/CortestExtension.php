<?php

namespace App\Twig;

use App\Entity\EchelleGraphique;
use App\Entity\ReponseCandidat;
use App\Entity\Subtest;
use App\Repository\EchelleGraphiqueRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

    public function getFilters(): array
    {
        return [
            new TwigFilter('sexe', [$this, 'formatSexe']),
            new TwigFilter('eirs', [$this, 'formatEirs']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction("properties_starting_with", [$this, "propertiesStartingWith"])
        ];
    }

    public function propertiesStartingWith($object, $prefix): array
    {
        $array = (array)$object;
        $result = [];
        foreach ($array as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $result[$key] = $value;
            }
        }

        return $result;
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