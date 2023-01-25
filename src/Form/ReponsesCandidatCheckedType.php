<?php

namespace App\Form;

use App\Form\Data\ReponsesCandidatChecked;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponsesCandidatCheckedType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "checked",
            CheckboxType::class,
            [
                "required" => false
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", ReponsesCandidatChecked::class);
    }
}