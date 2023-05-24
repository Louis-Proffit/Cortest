<?php

namespace App\Form;

use App\Entity\Subtest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SubtestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("nom", TextType::class)
            ->add("type", ChoiceType::class, ["choices" => Subtest::TYPES_SUBTEST_CHOICES])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }
}