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

class RechercheFiltreType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("filtre_prenom", TextType::class, ["empty_data" => "", "required" => false])
            ->add("filtre_nom", TextType::class, ["empty_data" => "", "required" => false])
            ->add("filtre_date_de_naissance_min", DateType::class)
            ->add("filtre_date_de_naissance_max", DateType::class)
            ->add("submit", SubmitType::class, ["label" => "Filtrer"]);
    }
}