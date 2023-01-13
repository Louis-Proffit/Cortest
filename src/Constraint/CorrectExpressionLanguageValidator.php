<?php

namespace App\Constraint;

use App\Core\Res\Correcteur\CorrecteurManager;
use App\Core\Res\Grille\GrilleRepository;
use App\Entity\Correcteur;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CorrectExpressionLanguageValidator extends ConstraintValidator
{

    public function __construct(
        private readonly GrilleRepository  $grille_repository,
        private readonly CorrecteurManager $correcteur_manager
    )
    {
    }

    /**
     * @param Correcteur $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CorrectExpressionLanguageConstraint) {
            throw new UnexpectedTypeException($constraint, CorrectExpressionLanguageConstraint::class);
        }

        if (!$value instanceof Correcteur) {
            throw new UnexpectedTypeException($value, Correcteur::class);
        }

        $grille = $this->grille_repository->get($value->grille_id);

        foreach ($grille->getTestGrilleInstances() as $grille_instance) {
            try {
                // Reponse candidat $this->correcteur_manager->corriger($value, $grille_instance);
            } catch (Exception $e) {
            }
        }

    }
}