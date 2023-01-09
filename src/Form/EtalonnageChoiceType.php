<?php

namespace App\Form;

use App\Entity\Etalonnage;
use App\Repository\EtalonnageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtalonnageChoiceType extends AbstractType
{
    const SCORE_ID_OPTION = "score_id";

    public function __construct(
        private readonly EtalonnageRepository $repository
    )
    {
    }

    private function etalonnage(Etalonnage $definition_etalonnage_computer): string
    {
        return $definition_etalonnage_computer->nom;
    }

    private function definitionEtalonnageComputerChoices(int $score_id): array
    {
        $etalonnages = $this->repository->findBy(["score_id" => $score_id]);

        $result = [];
        foreach ($etalonnages as $etalonnage) {
            $result[$etalonnage->nom] = $etalonnage;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "etalonnage",
            ChoiceType::class,
            [
                "choices" => $this->definitionEtalonnageComputerChoices($options[self::SCORE_ID_OPTION])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::SCORE_ID_OPTION);
        $resolver->setAllowedTypes(self::SCORE_ID_OPTION, "int");
    }

}