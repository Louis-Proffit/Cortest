<?php

namespace App\Constraint;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Grille\GrilleRepository;
use App\Core\Renderer\RendererRepository;
use App\Entity\Correcteur;
use App\Entity\EchelleGraphique;
use App\Entity\Graphique;
use Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsGraphiqueOptionsValidator extends ConstraintValidator
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
        if (!$constraint instanceof IsGraphiqueOptions) {
            throw new UnexpectedTypeException($constraint, IsGraphiqueOptions::class);
        }

        /** @var Graphique $object */
        $object = $this->context->getObject();


        if (!$object instanceof Graphique) {
            throw new UnexpectedTypeException($object, Graphique::class);
        }

        $renderer = $this->renderer_repository->fromIndex($object->renderer_index);

        foreach ($object->options as $key => $value) {

            $valid = false;

            foreach ($renderer->getOptions() as $options) {
                if ($options->nom_php === $key) {
                    $valid = true;
                    break;
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