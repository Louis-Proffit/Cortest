<?php

namespace App\Form;

use App\Repository\ConcoursRepository;
use App\Repository\GrilleRepository;
use App\Repository\SgapRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class SessionType extends AbstractType
{

    public function __construct(
        private readonly SgapRepository     $sgap_repository,
        private readonly ConcoursRepository $concours_repository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("date", DateType::class)
            ->add("concours", ChoiceType::class, [
                "choices" => $this->concours_repository->choices()
            ])
            ->add("date", DateType::class)
            ->add("sgap", ChoiceType::class, [
                "choices" => $this->sgap_repository->choices()
            ])
            ->add("observations", TextareaType::class)
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}