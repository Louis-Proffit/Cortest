<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrecteurType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("nom", TextType::class)
            ->add("tests", EntityType::class, [
                "class" => Test::class,
                "choice_label" => "nom",
                "multiple" => true,
                "expanded" => true
            ])
            ->add(
                "echelles", CollectionType::class, [
                    "entry_type" => EchelleCorrecteurType::class,
                    "entry_options" => ["label" => false]
                ]
            )
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}