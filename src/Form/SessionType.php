<?php

namespace App\Form;

use App\Core\Grille\GrilleRepository;
use App\Repository\ConcoursRepository;
use App\Repository\SgapRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SessionType extends AbstractType
{

    public function __construct(
        private readonly SgapRepository     $sgap_repository,
        private readonly ConcoursRepository $concours_repository,
        private readonly GrilleRepository   $grille_repository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("date", DateType::class)
            ->add("grille_class", ChoiceType::class, [
                "choices" => $this->grille_repository->nomToClassName()
            ])
            ->add("concours", ChoiceType::class, [
                "choices" => $this->concours_repository->choices()
            ])
            ->add("type_concours", IntegerType::class)
            ->add("version_batterie", IntegerType::class)
            ->add("date", DateType::class)
            ->add("sgap", ChoiceType::class, [
                "choices" => $this->sgap_repository->choices()
            ])
            ->add("observations", TextareaType::class)
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}