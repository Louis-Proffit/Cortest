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

class GraphiqueSubtestsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nouveau_Subtest', TextType::class)
            ->add('Type_Subtest', ChoiceType::class, ['choices' => Graphique::TYPE_SUBTEST])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }
}