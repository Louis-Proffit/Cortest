<?php

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueDTOValidator extends ConstraintValidator
{
    public function __construct()
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueDTO) {
            throw new UnexpectedTypeException($constraint, UniqueDTO::class);
        }

        $repository = $constraint->repository;

        $criteria = $repository->findBy(array($constraint->field => $value));

        if (count($criteria)) {
            $cvb = $this->context->buildViolation($constraint->message);

            if ($constraint->atPath) {
                $cvb->atPath($constraint->atPath);
            }

            $cvb->addViolation();
        }
    }
}