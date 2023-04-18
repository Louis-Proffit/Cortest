<?php

namespace App\Form;

use App\Constraint\UniqueDTO;
use App\Entity\Etalonnage;
use App\Form\Data\EtalonnageGaussienCreer;
use App\Form\EchelleEtalonnageGaussienType;
use App\Repository\EtalonnageRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
