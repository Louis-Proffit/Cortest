<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Grille\GrilleRepository;
use App\Entity\Correcteur;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhpIdentifierValidator extends ConstraintValidator
{
    const VIOLATION_MESSAGE = "L'identifiant est incorrect";

    /**
     * @param string $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PhpIdentifier) {
            throw new UnexpectedTypeException($constraint, PhpIdentifier::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, "string");
        }

        if (!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $value)) {
            $this->context->addViolation(self::VIOLATION_MESSAGE);
        }
    }
}