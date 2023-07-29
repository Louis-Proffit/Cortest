<?php

namespace App\Form;

use App\Entity\Etalonnage;
use App\Entity\Structure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtalonnageChoiceType extends AbstractType
{

    const OPTION_STRUCTURE = "structure";

    private function etalonnageChoices(Structure $structure): array
    {

        $result = [];

        /** @var Etalonnage $etalonnage */
        foreach ($structure->etalonnages as $etalonnage) {
            $result[$etalonnage->nom] = $etalonnage;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            "etalonnage",
            ChoiceType::class,
            [
                "choices" => $this->etalonnageChoices($options[self::OPTION_STRUCTURE])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::OPTION_STRUCTURE);
        $resolver->setAllowedTypes(self::OPTION_STRUCTURE, Structure::class);
    }

}