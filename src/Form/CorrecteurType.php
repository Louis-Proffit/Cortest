<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrecteurType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("nom", TextType::class)
            ->add(
                "values", CollectionType::class, [
                    "entry_type" => TextType::class,
                    "entry_options" => ["label" => false]
                ]
            )
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}