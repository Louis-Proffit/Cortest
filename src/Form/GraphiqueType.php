<?php

namespace App\Form;

use App\Core\Renderer\Renderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GraphiqueType extends AbstractType
{

    const OPTION_RENDERER = "renderer";

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Renderer $renderer */
        $renderer = $options[self::OPTION_RENDERER];

        $builder->add("nom", TextType::class)
            ->add("options", GraphiqueOptionsType::class, [self::OPTION_RENDERER => $renderer])
            ->add("echelles", CollectionType::class, [
                'entry_type' => EchelleGraphiqueType::class,
                'entry_options' => [
                    self::OPTION_RENDERER => $renderer
                ]
            ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->define(self::OPTION_RENDERER);
        $resolver->setAllowedTypes(self::OPTION_RENDERER, Renderer::class);
    }


}