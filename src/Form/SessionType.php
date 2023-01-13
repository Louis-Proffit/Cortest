<?php

namespace App\Form;

use App\Core\Grille\GrilleRepository;
use App\Repository\SgapRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SessionType extends AbstractType
{

    public function __construct(
        private readonly SgapRepository   $sgap_repository,
        private readonly GrilleRepository $grille_repository
    )
    {
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
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}