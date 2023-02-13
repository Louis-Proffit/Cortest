<?php

namespace App\Form;

use App\Repository\ConcoursRepository;
use App\Repository\NiveauScolaireRepository;
use App\Repository\SessionRepository;
use App\Repository\SgapRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheFiltreType extends AbstractType
{

    public function __construct(
        private readonly SessionRepository $session_repository,
        private readonly NiveauScolaireRepository $niveau_scolaire_repository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("filtre_prenom", TextType::class, ["empty_data" => "", "required" => false])
            ->add("filtre_nom", TextType::class, ["empty_data" => "", "required" => false])
            ->add("filtre_date_de_naissance_min", DateType::class)
            ->add("filtre_date_de_naissance_max", DateType::class)
            ->add("niveau_scolaire", ChoiceType::class, [
                "choices" => $this->niveau_scolaire_repository->nullable_choices()
            ])
            ->add("session", ChoiceType::class, [
                "choices" => $this->session_repository->nullable_choices()
            ])
            ->add("submit", SubmitType::class, ["label" => "Filtrer"]);
    }
}