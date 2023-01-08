<?php

namespace App\Twig;

use App\Constants\Sgaps;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CortestExtension extends AbstractExtension
{

    public function __construct(
        private readonly Sgaps $sgaps
    )
    {
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
        return $this->sgaps->nom($sgap_index);
    }
}