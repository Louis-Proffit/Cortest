<?php

namespace App\Form;

use App\Entity\DefinitionProfilComputer;
use App\Entity\DefinitionScore;
use App\Repository\DefinitionProfilComputerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametresCalculProfilType extends AbstractType
{
    const DEFINITION_SCORE_OPTION = "definition_etalonnage";

    public function __construct(
        private readonly DefinitionProfilComputerRepository $repository
    )
    {
    }

    private function definitionEtalonnageComputerDisplay(DefinitionProfilComputer $definition_etalonnage_computer): string
    {
        return $definition_etalonnage_computer->nom;
    }

    private function definitionEtalonnageComputerChoices(DefinitionScore $definition_score): array
    {
        $definition_etalonnage_computers = $this->repository->findByScoreDefinition($definition_score);

        $result = [];
        foreach ($definition_etalonnage_computers as $definition_etalonnage_computer) {
            $result[$this->definitionEtalonnageComputerDisplay($definition_etalonnage_computer)] = $definition_etalonnage_computer;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "definition_etalonnage_computer",
            ChoiceType::class,
            [
                "choices" => $this->definitionEtalonnageComputerChoices($options[self::DEFINITION_SCORE_OPTION])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::DEFINITION_SCORE_OPTION);
        $resolver->setAllowedTypes(self::DEFINITION_SCORE_OPTION, DefinitionScore::class);
    }

}