<?php

namespace App\Form;

use App\Repository\ReponseCandidatRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheReponsesCandidatType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("filtre_prenom", TextType::class, ["empty_data" => ""])
            ->add("filtre_nom", TextType::class, ["empty_data" => ""])
            ->add("filtre_date_de_naissance_min", DateType::class)
            ->add("filtre_date_de_naissance_max", DateType::class)
            ->add(
                "reponses_candidat",
                CollectionType::class,
                [
                    "entry_type" => ReponsesCandidatCheckedType::class
                ]
            )->add("filter", SubmitType::class, ["label" => "Filtrer"])
            ->add("save", SubmitType::class, ["label" => "Enregistrer"]);
    }
}