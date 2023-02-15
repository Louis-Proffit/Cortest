<?php

namespace App\Form;

use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Form\Mapper\CollectionToArrayTransformer;
use App\Repository\CorrecteurRepository;
use App\Repository\EchelleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{

    public function __construct(
        private readonly EchelleRepository       $echelle_repository,
        private readonly CollectionToArrayTransformer $collection_to_array_mapper
    )
    {
    }

    private function echelleChoices(): array
    {
        $items = $this->echelle_repository->findAll();

        return array_combine(
            array_map(fn(Echelle $echelle) => $echelle->nom . " (" . $echelle->nom_php . ")", $items),
            $items
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("nom", TextType::class)
            ->add("echelles", ChoiceType::class, [
                "choices" => $this->echelleChoices(),
                "multiple" => true,
                "expanded" => true
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);

        $builder->get("echelles")->addModelTransformer($this->collection_to_array_mapper);
    }

}