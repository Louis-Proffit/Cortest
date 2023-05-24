<?php

namespace App\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Compilable extends Constraint
{

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);
    }
}