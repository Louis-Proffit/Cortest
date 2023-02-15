<?php

namespace App\Constraint;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ClassNameValidator extends ConstraintValidator
{
    const VIOLATION_MESSAGE = "Ce devrait être un nom de classe";

    /**
     * @param string $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ClassName) {
            throw new UnexpectedTypeException($constraint, ClassName::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, "string");
        }

        if (!class_exists($value)) {
            $this->context->addViolation(self::VIOLATION_MESSAGE);
        }
    }
}