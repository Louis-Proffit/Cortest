<?php

namespace App\Form;

use App\Core\Renderer\Renderer;
use App\Entity\EchelleGraphique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchelleGraphiqueType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Renderer $renderer */
        $renderer = $options[GraphiqueType::OPTION_RENDERER];

        $builder->add("options", EchelleGraphiqueOptionsType::class, [GraphiqueType::OPTION_RENDERER => $renderer]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EchelleGraphique::class,
        ]);

        $resolver->define(GraphiqueType::OPTION_RENDERER);
        $resolver->setAllowedTypes(GraphiqueType::OPTION_RENDERER, Renderer::class);
    }

}