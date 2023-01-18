<?php

namespace App\Form;

use App\Core\Renderer\Renderer;
use App\Entity\EchelleGraphique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchelleGraphiqueOptionsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Renderer $renderer */
        $renderer = $options[GraphiqueType::OPTION_RENDERER];

        foreach ($renderer->getEchelleOptions() as $option) {
            $builder->add($option->nom_php, $option->form_type, ["label" => $option->nom]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(GraphiqueType::OPTION_RENDERER);
        $resolver->setAllowedTypes(GraphiqueType::OPTION_RENDERER, Renderer::class);
    }

}