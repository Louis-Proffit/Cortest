<?php

namespace App\Form;

use App\Entity\Echelle;
use App\Form\Mapper\CollectionToArrayTransformer;
use App\Repository\EchelleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Positive;

class CreerStructureType extends AbstractType
{

    public const ECHELLES_COUNT_KEY = "echelles_count";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("nom", TextType::class, ["label" => "Nom de la structure"])
            ->add(self::ECHELLES_COUNT_KEY, IntegerType::class, [
                "label" => "Nombre d'échelles",
                "mapped" => false,
                "constraints" => [new Positive()]
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }
}