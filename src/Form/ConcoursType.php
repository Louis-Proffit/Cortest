<?php

namespace App\Form;

use App\Form\Mapper\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConcoursType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("intitule", TextType::class, ["label" => "Intitulé du concours"])
            ->add("type_concours", IntegerType::class, ["label" => "Type de concours"]);
    }

}