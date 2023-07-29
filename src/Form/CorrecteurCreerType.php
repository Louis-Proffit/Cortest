<?php

namespace App\Form;

use App\Repository\StructureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrecteurCreerType extends AbstractType
{

    public function __construct(
        private readonly StructureRepository $structureRepository
    )
    {
    }

    public
    function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("nom", TextType::class, ["label" => "Nom"])
            ->add("structure", ChoiceType::class, [
                "choices" => $this->structureRepository->choices()
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}