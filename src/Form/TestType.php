<?php

namespace App\Form;

use App\Entity\Concours;
use App\Form\Mapper\CollectionToArrayTransformer;
use App\Repository\ConcoursRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TestType extends AbstractType
{

    public function __construct(
        private readonly CollectionToArrayTransformer $collectionToArrayTransformer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("nom", TextType::class, ["label" => "Nom du test"])
            ->add("version_batterie", IntegerType::class, ["label" => "Version batterie"])
            ->add("concours", EntityType::class, [
                "class" => Concours::class,
                "choice_label" => fn(Concours $concours) => $concours->summary(),
                "multiple" => true,
                "expanded" => true
            ])
            ->add("questions", CollectionType::class, [
                "entry_type" => QuestionTestType::class,
                "label" => false
            ]);

        $builder->get("concours")->addModelTransformer($this->collectionToArrayTransformer);
        $builder->get("questions")->addModelTransformer($this->collectionToArrayTransformer);
    }

}