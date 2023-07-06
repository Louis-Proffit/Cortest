<?php

namespace App\Form;

use App\Entity\QuestionConcours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionConcoursType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("type", ChoiceType::class, [
            "choices" => array_combine(QuestionConcours::TYPES, QuestionConcours::TYPES)
        ])->add("intitule", TextType::class, ["label" => "Intitulé"])
            ->add("abreviation", TextType::class, ["label" => "Abréviation"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault("data_class", QuestionConcours::class);
    }

}