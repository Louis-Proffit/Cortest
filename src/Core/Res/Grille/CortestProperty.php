<?php

namespace App\Core\Res\Grille;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[Attribute]
class CortestProperty
{
    public string $nom;

    #[HasNamedArguments]
    public function __construct(string $nom)
    {
        $this->nom = $nom;
    }


}