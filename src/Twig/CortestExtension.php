<?php

namespace App\Twig;

use App\Entity\ReponseCandidat;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CortestExtension extends AbstractExtension
{


    public function getFilters(): array
    {
        return [
            new TwigFilter('sexe', [$this, 'formatSexe']),
        ];
    }

    public function formatSexe(int $sex_index): string
    {
        if ($sex_index == ReponseCandidat::INDEX_HOMME) {
            return "Homme";
        } else {
            return "Femme";
        }
    }
}