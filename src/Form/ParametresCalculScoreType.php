<?php

namespace App\Form;

use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScoreComputer;
use App\Repository\DefinitionScoreComputerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametresCalculScoreType extends AbstractType
{
    const DEFINITION_GRILLE_OPTION = "definition_grille";

    public function __construct(
        private readonly DefinitionScoreComputerRepository $repository
    )
    {
    }

    private function definitionScoreComputerDisplay(DefinitionScoreComputer $definition_score_computer): string
    {
        return $definition_score_computer->nom;
    }

    private function definitionScoreComputerChoices(DefinitionGrille $definition_grille): array
    {
        $definition_score_computers = $this->repository->findByGrilleDefinition($definition_grille);

        $result = [];
        foreach ($definition_score_computers as $definition_score_computer) {
            $result[$this->definitionScoreComputerDisplay($definition_score_computer)] = $definition_score_computer;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "definition_score_computer",
            ChoiceType::class,
            [
                "choices" => $this->definitionScoreComputerChoices($options[self::DEFINITION_GRILLE_OPTION])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::DEFINITION_GRILLE_OPTION);
        $resolver->setAllowedTypes(self::DEFINITION_GRILLE_OPTION, DefinitionGrille::class);
    }

}