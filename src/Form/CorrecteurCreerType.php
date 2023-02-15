<?php

namespace App\Form;

use App\Constraint\UniqueDTO;
use App\Repository\ConcoursRepository;
use App\Repository\CorrecteurRepository;
use App\Repository\GrilleRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrecteurCreerType extends AbstractType
{

    public function __construct(
        private readonly ProfilRepository     $profil_repository,
        private readonly ConcoursRepository   $concours_repository,
        private readonly CorrecteurRepository $correcteurRepository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("profil", ChoiceType::class, [
                "choices" => $this->profil_repository->choices()
            ])
            ->add("concours", ChoiceType::class, [
                "choices" => $this->concours_repository->choices()
            ])
            ->add("nom",
                TextType::class,
                ['constraints' => new UniqueDTO(field: 'nom',
                    message: 'Ce nom existe deja',
                    repository: $this->correcteurRepository)])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}