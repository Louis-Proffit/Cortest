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

class GraphiqueFooterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nouveau_Bas_De_Cadre', ChoiceType::class, ['choices' => $options['choices']])
            ->add('Type_Bas_De_Cadre', ChoiceType::class, ['choices' => Graphique::TYPE_FOOTER])
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