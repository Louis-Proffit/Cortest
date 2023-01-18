<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Grille\GrilleRepository;
use App\Entity\Correcteur;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ClassNameValidator extends ConstraintValidator
{

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

        if(!class_exists($value)) {
            $this->context->addViolation("Ce devrait être un nom de classe");
        }
    }
}