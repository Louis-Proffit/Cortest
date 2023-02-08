<?php

namespace App\Form;

use App\Constraint\UniqueDTO;
use App\Core\Grille\GrilleRepository;
use App\Repository\ProfilRepository;
use App\Repository\CorrecteurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrecteurCreerType extends AbstractType
{

    public function __construct(
        private readonly ProfilRepository $profil_repository,
        private readonly GrilleRepository $grille_repository,
        private readonly CorrecteurRepository $correcteurRepository,
    )
    {
    }

    private function profilChoices(): array
    {
        $items = $this->profil_repository->findAll();

        $result = [];

        foreach ($items as $item) {
            $result[$item->nom] = $item;
        }

        return $result;
    }

    private function grilleChoices(): array
    {
        return $this->grille_repository->nomToClassName();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("profil", ChoiceType::class, [
                "choices" => $this->profilChoices()
            ])
            ->add("grille_class", ChoiceType::class, [
                "choices" => $this->grilleChoices()
            ])
            ->add("nom", TextType::class, ['constraints' => new UniqueDTO(field:'nom', message: 'Ce nom existe deja', repository: $this->correcteurRepository)])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}