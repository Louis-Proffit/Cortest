<?php

namespace App\Form;

use App\Entity\Graphique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GraphiqueBRMRType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nouvelle_Echelle', ChoiceType::class, ['choices' => $options['choices']])
            ->add('Echelle_Bonnes_Reponses', ChoiceType::class, ['choices' => $options['choices']])
            ->add('Echelle_Mauvaises_Reponses', ChoiceType::class, ['choices' => $options['choices']])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => false,
        ]);
        $resolver->setAllowedTypes('choices', 'array');
    }
}