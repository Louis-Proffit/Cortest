<?php

namespace App\Form;

use App\Entity\Etalonnage;
use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtalonnageChoiceType extends AbstractType
{

    const OPTION_PROFIL = "profil";

    private function etalonnageChoices(Profil $profil): array
    {

        $result = [];

        /** @var Etalonnage $etalonnage */
        foreach ($profil->etalonnages as $etalonnage) {
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
                "choices" => $this->etalonnageChoices($options[self::OPTION_PROFIL])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::OPTION_PROFIL);
        $resolver->setAllowedTypes(self::OPTION_PROFIL, Profil::class);
    }

}