<?php

namespace App\Form\Generic;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CortestDateType extends DateType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
                "min_year" => 1900,
                "max_year" => (int)date('Y'),
                "choice_translation_domain" => true
            ]
        );

        $resolver->setDefault(
            'years',
            function (Options $options) {
                return range($options["min_year"], $options["max_year"]);
            }
        );
    }
}