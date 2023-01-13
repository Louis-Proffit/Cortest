<?php

namespace App\Form;

use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EtalonnageCreerType extends AbstractType
{

    public function __construct(
        private readonly ProfilRepository $profil_repository,
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("profil", ChoiceType::class, [
                "choices" => $this->profilChoices()
            ])
            ->add("nom", TextType::class)
            ->add("nombre_classes", IntegerType::class, ["label" => "Nombre de classes"])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}