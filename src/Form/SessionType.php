<?php

namespace App\Form;

use App\Repository\SgapRepository;
use App\Repository\TestRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class SessionType extends AbstractType
{

    public function __construct(
        private readonly SgapRepository $sgapRepository,
        private readonly TestRepository $testRepository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("date", CortestDateType::class)
            ->add("test", ChoiceType::class, [
                "choices" => $this->testRepository->choices()
            ])
            ->add("sgap", ChoiceType::class, [
                "choices" => $this->sgapRepository->choices()
            ])
            ->add("observations", TextareaType::class)
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}