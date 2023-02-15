<?php

namespace App\Form;

use App\Entity\Correcteur;
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

    public function __construct(
        private readonly CorrecteurRepository $repository
    )
    {
    }

    private function definitionCorrecteurChoice(Session $session): array
    {
        $correcteurs = $this->repository->findBy(["concours" => $session->concours]);

        $result = [];
        foreach ($correcteurs as $correcteur) {
            $result[$correcteur->nom] = $correcteur;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "correcteur",
            ChoiceType::class,
            [
                "choices" => $this->definitionCorrecteurChoice($options[self::OPTION_SESSION])
            ]
        )->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::OPTION_SESSION);
        $resolver->setAllowedTypes(self::OPTION_SESSION, Session::class);
    }

}