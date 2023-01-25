<?php

namespace App\Form;

use App\Repository\ReponseCandidatRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ReponsesCandidatCheckedListeType extends AbstractType
{

    const CALCUL_SCORE_KEY = "scores";
    const EXPORTER_REPONSES_CSV_KEY = "exporter_csv";

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "reponses_candidat",
            CollectionType::class,
            [
                "entry_type" => ReponsesCandidatCheckedType::class
            ]
        )->add(self::CALCUL_SCORE_KEY, SubmitType::class, ["label" => "Calculer les scores"])
            ->add(self::EXPORTER_REPONSES_CSV_KEY, SubmitType::class, ["label" => "Exporter les réponses"]);
    }
}