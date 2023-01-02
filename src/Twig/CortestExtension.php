<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CortestExtension extends AbstractExtension
{

    public function getFilters():array
    {
        return [
            new TwigFilter('sexe', [$this, 'formatSexe']),
            new TwigFilter('sgap', [$this, 'formatSgap']),
        ];
    }

    public function formatSexe(int $sex_index): string
    {
       if($sex_index == 0) {
           return "Homme";
       } else {
           return "Femme";
       }
    }

    public function formatSgap(int $sgap_index): string
    {
        if($sgap_index == 0) {
            return "SGAP 1";
        } else {
            return "Autre SGAP";
        }
    }
}