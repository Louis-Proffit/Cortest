<?php

namespace App\Form;

use App\Entity\Session;
use App\Repository\CorrecteurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorrecteurChoiceType extends AbstractType
{
    const OPTION_SESSION = "session";


    private function definitionCorrecteurChoice(Session $session): array
    {
        $result = [];

        foreach ($session->test->correcteurs as $correcteur) {
            $result[$correcteur->nom] = $correcteur;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            "correcteur",
            ChoiceType::class,
            [
                "choices" => $this->definitionCorrecteurChoice($options[self::OPTION_SESSION]),
                "label" => 'Correction',
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define(self::OPTION_SESSION);
        $resolver->setAllowedTypes(self::OPTION_SESSION, Session::class);
    }

}