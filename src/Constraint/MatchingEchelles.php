<?php

namespace App\Constraint;

use Attribute;
use Closure;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class MatchingEchelles extends Constraint
{
    public string $profil_property_name;
    public string $echelles_property_name;
    public string $sub_echelle_property_name;
    public Closure $init;

    #[HasNamedArguments]
    public function __construct(string  $profil_property_name,
                                string  $echelles_property_name,
                                string  $sub_echelle_property_name,
                                Closure $init,
                                mixed   $options = null,
                                array   $groups = null,
                                mixed   $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->profil_property_name = $profil_property_name;
        $this->echelles_property_name = $echelles_property_name;
        $this->sub_echelle_property_name = $sub_echelle_property_name;
    }


    public function getTargets(): array
    {
        return [Attribute::TARGET_CLASS];
    }
}