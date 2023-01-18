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

        throw new Exception("TODO");
    }
}