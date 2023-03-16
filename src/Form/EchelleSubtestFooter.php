<?php

namespace App\Form;

use App\Entity\EchelleGraphique;
use App\Entity\Subtest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchelleSubtestFooter extends AbstractType
{
    private function choices(array $echelles): array
    {
        $result = [];

        /** @var EchelleGraphique $echelle */
        foreach ($echelles as $echelle) {
            $result[$echelle->options[EchelleGraphique::OPTION_NOM_AFFICHAGE_PHP] . "(" . $echelle->echelle->nom . ")"] = $echelle;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('echelle', ChoiceType::class, ['choices' => $this->choices($options["echelles"])])
            ->add('type', ChoiceType::class, ['choices' => Subtest::TYPES_FOOTER_CHOICES])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(["echelles" => false,]);
        $resolver->setAllowedTypes("echelles", 'array');
    }
}