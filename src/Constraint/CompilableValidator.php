<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Grille\GrilleRepository;
use App\Entity\Correcteur;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Throwable;

class CompilableValidator extends ConstraintValidator
{

    public function __construct(
        private readonly CortestExpressionLanguage $cortest_expression_language
    )
    {
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Compilable) {
            throw new UnexpectedTypeException($constraint, Compilable::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, "string");
        }

        try {
            $this->cortest_expression_language->compile($value);
        } catch (Throwable $e) {
            $this->context->addViolation("Erreur de syntaxe " . $e);
        }
    }
}