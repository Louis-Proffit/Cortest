<?php

namespace App\Form;

use App\Entity\Correcteur;
use App\Repository\CorrecteurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametresCalculScoreType extends AbstractType
{
    const GRILLE_ID_OPTION = "grille_id";

    public function __construct(
        private readonly CorrecteurRepository $repository
    )
    {
    }

    private function definitionScoreComputerDisplay(Correcteur $correcteur): string
    {
        return $correcteur->nom;
    }

    private function definitionScoreComputerChoices(int $grille_id): array
    {
        $correcteurs = $this->repository->findBy(["grille_id" => $grille_id]);

        $result = [];
        foreach ($correcteurs as $correcteur) {
            $result[$this->definitionScoreComputerDisplay($correcteur)] = $correcteur;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "correcteur",
            ChoiceType::class,
            [
                "choices" => $this->definitionScoreComputerChoices($options[self::GRILLE_ID_OPTION])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::GRILLE_ID_OPTION);
        $resolver->setAllowedTypes(self::GRILLE_ID_OPTION, "int");
    }

}