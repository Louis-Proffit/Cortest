<?php

namespace App\Constraint;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

#[Attribute]
class CortestPassword extends Compound
{

    #[HasNamedArguments]
    public function __construct(private readonly int $min, mixed $options = null)
    {
        parent::__construct($options);
    }

    protected function getConstraints(array $options): array
    {
        return [
            new NotBlank(),
            new Length(min: $this->min, minMessage: "Le mot de passe doit faire plus de " . $this->min . " caractères"),
            new Type("string")
        ];
    }
}