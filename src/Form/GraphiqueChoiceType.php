<?php

namespace App\Form;

use App\Entity\Graphique;
use App\Entity\Structure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GraphiqueChoiceType extends AbstractType
{
    const OPTION_PROFIL = "profil";

    private function graphiqueChoices(Structure $profil): array
    {
        $graphiques = $profil->graphiques;

        $result = [];
        /** @var Graphique $graphique */
        foreach ($graphiques as $graphique) {
            $result[$graphique->nom] = $graphique;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "graphique",
            ChoiceType::class,
            [
                "choices" => $this->graphiqueChoices($options[self::OPTION_PROFIL])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::OPTION_PROFIL);
        $resolver->setAllowedTypes(self::OPTION_PROFIL, Structure::class);
    }
}