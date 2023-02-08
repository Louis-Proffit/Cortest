<?php

namespace App\Form;

use App\Entity\CortestUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConcoursType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("nom", TextType::class, ["label" => "Nom du concours"])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}