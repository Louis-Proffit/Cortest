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

    public function __construct(
        private readonly CollectionToArrayTransformer $collection_to_array_mapper
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("nom", TextType::class, ["label" => "Nom du concours"])
            ->add("type_concours", TextType::class, ["label" => "Type de concours"])
            ->add("version_batterie", TextType::class, ["label" => "Version batterie"])
            ->add("questions", CollectionType::class, [
                "entry_type" => QuestionConcoursType::class,
                "label" => false
            ]);

        $builder->get("questions")->addModelTransformer($this->collection_to_array_mapper);
    }

}