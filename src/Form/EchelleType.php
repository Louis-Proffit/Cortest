<?php

namespace App\Form;

use App\Entity\Echelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchelleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("nom", TextType::class)
            ->add("nom_php", TextType::class)
            ->add("type", ChoiceType::class, [
                "choices" => array_combine(Echelle::TYPE_ECHELLE_OPTIONS, Echelle::TYPE_ECHELLE_OPTIONS)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault("data_class", Echelle::class);
    }

}