<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ReponsesCandidatCheckedType extends AbstractType
{

    public function __construct(
        private readonly RouterInterface $router
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAction($this->router->generate("recherche_selectionner"))
            ->add(
                "checked",
                CollectionType::class,
                [
                    "entry_type" => CheckboxType::class,
                    "entry_options" => ["label" => false, "required" => false]
                ])
            ->add("submit", SubmitType::class, ["label" => "Sélectionner"]);
    }
}