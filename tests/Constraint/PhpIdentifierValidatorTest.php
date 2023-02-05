<?php

namespace App\Tests\Constraint;

use App\Constraint\PhpIdentifier;
use App\Constraint\PhpIdentifierValidator;
use Symfony\Component\Validator\Constraint;

class PhpIdentifierValidatorTest extends CortestConstraintValidatorTestCase
{

    protected function createValidator(): PhpIdentifierValidator
    {
        return new PhpIdentifierValidator();
    }

    public function provide(): array
    {
        return [
            ["x", null],
            ["xy", null],
            ["X", null],
            ["X_y", null],
            ["x8", null],
            ["8x", PhpIdentifierValidator::VIOLATION_MESSAGE],
            ["@", PhpIdentifierValidator::VIOLATION_MESSAGE],
            ["", PhpIdentifierValidator::VIOLATION_MESSAGE],
            ["x y", PhpIdentifierValidator::VIOLATION_MESSAGE],
            ["x-y", PhpIdentifierValidator::VIOLATION_MESSAGE],
        ];
    }

    public function geConstraint(): Constraint
    {
        return new PhpIdentifier();
    }
}
