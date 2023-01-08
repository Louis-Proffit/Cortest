<?php

namespace App\Form;

use App\Constants\Sgaps;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SessionType extends AbstractType
{

    public function __construct(
        private readonly Sgaps $sgaps
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "sgap_index",
            ChoiceType::class,
            ["choices" => $this->sgaps->nomToIndex()])
            ->add("date", DateType::class)
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}