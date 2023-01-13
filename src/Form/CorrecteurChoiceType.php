<?php

namespace App\Form;

use App\Entity\Correcteur;
use App\Repository\CorrecteurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorrecteurChoiceType extends AbstractType
{
    const GRILLE_CLASS_OPTION = "grille_id";

    public function __construct(
        private readonly CorrecteurRepository $repository
    )
    {
    }

    private function definitionCorrecteurChoice(string $grilleClass): array
    {
        $correcteurs = $this->repository->findBy(["grilleClass" => $grilleClass]);

        $result = [];
        foreach ($correcteurs as $correcteur) {
            $result[$correcteur->nom] = $correcteur;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "correcteur",
            ChoiceType::class,
            [
                "choices" => $this->definitionCorrecteurChoice($options[self::GRILLE_CLASS_OPTION])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::GRILLE_CLASS_OPTION);
        $resolver->setAllowedTypes(self::GRILLE_CLASS_OPTION, "string");
    }

}