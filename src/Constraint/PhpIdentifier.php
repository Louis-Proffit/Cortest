<?php

namespace App\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class PhpIdentifier extends Constraint
{

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);
    }
}