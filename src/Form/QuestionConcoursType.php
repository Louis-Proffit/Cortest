<?php

namespace App\Form;

use App\Entity\CortestUser;
use App\Entity\QuestionConcours;
use App\Repository\GrilleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionConcoursType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("type", ChoiceType::class, [
            "choices" => array_combine(QuestionConcours::TYPES, QuestionConcours::TYPES)
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", QuestionConcours::class);
    }

}