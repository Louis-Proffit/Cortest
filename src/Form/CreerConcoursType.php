<?php

namespace App\Form;

use App\Entity\CortestUser;
use App\Repository\GrilleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreerConcoursType extends AbstractType
{

    public function __construct(
        private readonly GrilleRepository $grille_repository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("nom", TextType::class, ["label" => "Nom du concours"])
            ->add("index_grille", ChoiceType::class, [
                "choices" => $this->grille_repository->indexChoices(),
                "label" => "Type de grille"
            ]);
    }

}