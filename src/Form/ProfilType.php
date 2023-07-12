<?php

namespace App\Form;

use App\Entity\Echelle;
use App\Form\Mapper\CollectionToArrayTransformer;
use App\Repository\EchelleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfilType extends AbstractType
{

    public function __construct(
        private readonly EchelleRepository            $echelle_repository,
        private readonly CollectionToArrayTransformer $collectionToArrayTransformer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("nom", TextType::class)
            ->add("echelles", CollectionType::class, [
                "entry_type" => EchelleType::class
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);

        $builder->get("echelles")->addModelTransformer($this->collectionToArrayTransformer);
    }

}