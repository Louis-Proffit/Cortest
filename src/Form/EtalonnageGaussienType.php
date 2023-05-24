<?php

namespace App\Form;

use App\Form\Data\EtalonnageGaussienCreer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtalonnageGaussienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('echelleEtalonnageGaussienCreer', CollectionType::class, [
                'label' => 'Échelle',
                'entry_type' => EchelleEtalonnageGaussienType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'entry_options' => [
                    'bounds_number' => $options['bounds_number'],
                ],
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EtalonnageGaussienCreer::class,
            'bounds_number' => 9, // default number of bounds
        ]);
    }
}
