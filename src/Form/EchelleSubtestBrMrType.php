<?php

namespace App\Form;

use App\Entity\EchelleGraphique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchelleSubtestBrMrType extends AbstractType
{

    private function choices(array $echelles): array
    {
        $result = [];

        /** @var EchelleGraphique $echelle */
        foreach ($echelles as $echelle) {
            $result[$echelle->options[EchelleGraphique::OPTION_NOM_AFFICHAGE_PHP] . " (" . $echelle->echelle->nom . ")"] = $echelle;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('echelle_br', ChoiceType::class, ['choices' => $this->choices($options["echelles"]), "label" => "Echelle de bonnes réponses"])
            ->add('echelle_mr', ChoiceType::class, ['choices' => $this->choices($options["echelles"]), "label" => "Echelle de mauvaises réponses"])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(["echelles" => false,]);
        $resolver->setAllowedTypes("echelles", 'array');
    }
}