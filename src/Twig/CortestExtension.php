<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CortestExtension extends AbstractExtension
{

    private array $sgaps;

    /**
     * @param array $sgaps
     */
    public function __construct(array $sgaps)
    {
        $this->sgaps = $sgaps;
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter('sexe', [$this, 'formatSexe']),
            new TwigFilter('sgap', [$this, 'formatSgap']),
        ];
    }

    public function formatSexe(int $sex_index): string
    {
        if ($sex_index == 0) {
            return "Homme";
        } else {
            return "Femme";
        }
    }

    public function formatSgap(int $sgap_index): string
    {
        if (array_key_exists($sgap_index, $this->sgaps)) {
            return $this->sgaps[$sgap_index];
        } else {
            return "SGAP inconnu";
        }
    }
}