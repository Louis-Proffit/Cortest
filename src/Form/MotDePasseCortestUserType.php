<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class MotDePasseCortestUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("password", RepeatedType::class, [
            "type" => PasswordType::class,
            'first_options' => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmer le mot de passe'],
        ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}