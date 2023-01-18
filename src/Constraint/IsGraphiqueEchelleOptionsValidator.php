<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Grille\GrilleRepository;
use App\Core\Renderer\RendererRepository;
use App\Entity\Correcteur;
use App\Entity\EchelleGraphique;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsGraphiqueEchelleOptionsValidator extends ConstraintValidator
{

    public function __construct(
        private readonly RendererRepository $renderer_repository
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
        if (!$constraint instanceof IsGraphiqueEchelleOptions) {
            throw new UnexpectedTypeException($constraint, IsGraphiqueEchelleOptions::class);
        }

        /** @var EchelleGraphique $object */
        $object = $this->context->getObject();


        if (!$object instanceof EchelleGraphique) {
            throw new UnexpectedTypeException($object, EchelleGraphique::class);
        }

        $renderer = $this->renderer_repository->fromIndex($object->graphique->renderer_index);

        foreach ($object->options as $key => $value) {

            $valid = true;

            foreach ($renderer->getEchelleOptions() as $options) {
                if ($options->nom_php === $key) {
                    $valid = false;
                }
            }

            if (!$valid) {
                $this->context->buildViolation("L'option n'existe pas : " . $key)
                    ->atPath("[" . $key . "]")
                    ->addViolation();
            }
        }
    }
}