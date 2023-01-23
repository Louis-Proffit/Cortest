<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Grille\GrilleRepository;
use App\Core\Renderer\RendererRepository;
use App\Entity\Correcteur;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RendererIndexValidator extends ConstraintValidator
{

    public function __construct(
        private readonly RendererRepository $renderer_repository
    )
    {
    }

    /**
     * @param int $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RendererIndex) {
            throw new UnexpectedTypeException($constraint, RendererIndex::class);
        }

        if (!$this->renderer_repository->indexExists($value)) {
            $this->context->addViolation("L'indice indiqué n'est pas celui d'un Renderer existant : " . $value);
        }
    }
}