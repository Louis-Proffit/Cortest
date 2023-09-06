<?php

namespace App\Form;

use App\Form\Generic\CortestDateType;
use App\Repository\NiveauScolaireRepository;
use App\Repository\SessionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ReponsesCandidatCheckedType extends AbstractType
{

    public function __construct(
        private readonly RouterInterface $router
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAction($this->router->generate("recherche_selectionner"))
            ->add(
                "this",
                CollectionType::class,
                ["entry_type" => ReponseCandidatCheckedType::class])
            ->add("submit", SubmitType::class, ["label" => "Sélectionner"]);
    }
}