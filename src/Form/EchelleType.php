<?php

namespace App\Form;

use App\Entity\Echelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EchelleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("nom", TextType::class)
            ->add("nom_php", TextType::class)
            ->add("type", ChoiceType::class, [
                "choices" => array_combine(Echelle::TYPE_ECHELLE_OPTIONS, Echelle::TYPE_ECHELLE_OPTIONS)
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}