<?php

namespace App\Tests\Constraint;

use App\Constraint\ClassName;
use App\Constraint\ClassNameValidator;
use Symfony\Component\Validator\Constraint;

class ClassNameValidatorTest extends CortestConstraintValidatorTestCase
{

    protected function createValidator(): ClassNameValidator
    {
        return new ClassNameValidator();
    }

    public function geConstraint(): Constraint
    {
        return new ClassName();
    }

    public function provide(): array
    {
        return [
            [ClassNameValidator::class, null],
            ["x", ClassNameValidator::VIOLATION_MESSAGE],
            ["string", ClassNameValidator::VIOLATION_MESSAGE]
        ];
    }
}
