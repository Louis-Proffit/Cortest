<?php

namespace App\Tests\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

abstract class CortestConstraintValidatorTestCase extends ConstraintValidatorTestCase
{
    public abstract function geConstraint(): Constraint;

    public abstract function provide(): array;

    /**
     * @dataProvider provide
     * @param string $input
     * @param string|null $violation
     * @return void
     */
    public function test(string $input, ?string $violation): void
    {
        $this->validator->validate($input, $this->geConstraint());

        if ($violation == null) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation($violation)->assertRaised();
        }
    }
}