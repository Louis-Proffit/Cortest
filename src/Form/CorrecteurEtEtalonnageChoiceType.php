<?php

namespace App\Form;

use App\Entity\Etalonnage;
use App\Entity\Session;
use App\Form\Data\CorrecteurEtEtalonnagePair;
use App\Repository\CorrecteurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorrecteurEtEtalonnageChoiceType extends AbstractType
{
    const OPTION_SESSION = "session";

    public function __construct(
        private readonly CorrecteurRepository $correcteur_repository
    )
    {
    }

    private function correcteurAndEtalonnageChoices(Session $session): array
    {
        $correcteurs = $this->correcteur_repository->findBy(["concours" => $session->concours]);

        $result = [];
        foreach ($correcteurs as $correcteur) {

            $sub_result = [];

            $profil = $correcteur->profil;

            /** @var Etalonnage $etalonnage */
            foreach ($profil->etalonnages as $etalonnage) {
                $sub_result[$correcteur->nom . " | " . $etalonnage->nom] = new CorrecteurEtEtalonnagePair($correcteur,
                    $etalonnage);
            }

            $result[$correcteur->nom] = $sub_result;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("both", ChoiceType::class, [
            "choices" => $this->correcteurAndEtalonnageChoices($options[self::OPTION_SESSION]),
            "label" => "Correcteur et étalonnage"
        ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::OPTION_SESSION);
        $resolver->setAllowedTypes(self::OPTION_SESSION, Session::class);
    }

}