<?php

namespace App\Form;

use App\Repository\GrilleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreerTestType extends AbstractType
{

    public function __construct(
        private readonly GrilleRepository $grilleRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("nom", TextType::class, ["label" => "Nom du test"])
            ->add("version_batterie", TextType::class, ["label" => "Version batterie"])
            ->add("index_grille", ChoiceType::class, [
                "choices" => $this->grilleRepository->indexChoices(),
                "label" => "Type de grille"
            ]);
    }

}