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

    private function concoursChoices(): array
    {
        $items = $this->concours_repository->findAll();

        $result = [];

        foreach ($items as $concours) {
            $result[$concours->nom] = $concours;
        }

        return $result;
    }

    private function sgapChoices(): array
    {
        $items = $this->sgap_repository->findAll();

        $result = [];

        foreach ($items as $sgap) {
            $result[$sgap->nom] = $sgap;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("sgap", ChoiceType::class, [
            "choices" => $this->sgapChoices()
        ])
            ->add("date", DateType::class)
            ->add("grille_class", ChoiceType::class, [
                "choices" => $this->grille_repository->nomToClassName()
            ])
            ->add("concours", ChoiceType::class, [
                "choices" => $this->concoursChoices()
            ])
            ->add("type_concours", IntegerType::class)
            ->add("version_batterie", IntegerType::class)
            ->add("date", DateType::class)
            ->add("sgap", ChoiceType::class, [
                "choices" => $this->sgapChoices()
            ])
            ->add("observations", TextareaType::class)
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}