<?php

namespace App\Form;

use App\Form\Generic\CortestDateType;
use App\Repository\ConcoursRepository;
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
        private readonly ConcoursRepository $concoursRepository,
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
            ->add("concours", ChoiceType::class, [
                "choices" => $this->concoursRepository->choices(),
                "label" => "Type Concours",
            ])
            ->add("sgap", ChoiceType::class, [
                "choices" => $this->sgapRepository->choices(),
                "label" => "SGAP",
            ])
            ->add("observations", TextareaType::class, ["empty_data" => ""])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}