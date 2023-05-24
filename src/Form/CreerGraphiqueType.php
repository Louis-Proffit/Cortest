<?php

namespace App\Form;

use App\Constraint\UniqueDTO;
use App\Core\Renderer\RendererRepository;
use App\Repository\GraphiqueRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreerGraphiqueType extends AbstractType
{

    public function __construct(
        private readonly RendererRepository $repository,
        private readonly ProfilRepository   $profil_repository,
        private readonly GraphiqueRepository $graphiqueRepository
    )
    {
    }

    private function profilChoices(): array
    {
        $result = [];

        foreach ($this->profil_repository->findAll() as $profil) {
            $result[$profil->nom] = $profil;
        }

        return $result;
    }

    private function rendererIndexChoice(): array
    {
        return $this->repository->nomToIndex();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("nom", TextType::class, ['constraints' => new UniqueDTO(field:'nom', message: 'Ce nom existe deja', repository: $this->graphiqueRepository)])
            ->add("profil", ChoiceType::class, [
                "choices" => $this->profilChoices(),
                "label" => "Profil d'entrée"
            ])
            ->add("renderer_index", ChoiceType::class, [
                "choices" => $this->rendererIndexChoice(),
                "label" => "Format de sortie"
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}