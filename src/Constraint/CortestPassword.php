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

    protected function getConstraints(array $options): array
    {
        return [
            new NotBlank(),
            new Length(min: 12),
            new Type("string")
        ];
    }
}