<?php

namespace App\Core\Grille;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[Attribute]
class CortestGrille
{

    public string $nom;
    public array $tests;

    #[HasNamedArguments]
    public function __construct(string $nom, array $tests)
    {
        $this->nom = $nom;
        $this->tests = $tests;
    }


}