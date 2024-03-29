<?php

namespace App\Form\Generic;

use App\Entity\CortestUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CortestUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("username", TextType::class, ["label" => "Nom d'utilisateur"])
            ->add("role", ChoiceType::class, [
                "choices" => array_combine(CortestUser::ROLES, CortestUser::ROLES),
                "label" => "Droits"
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}