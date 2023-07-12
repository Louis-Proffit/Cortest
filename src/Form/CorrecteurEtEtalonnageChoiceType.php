<?php

namespace App\Form;

use App\Entity\Concours;
use App\Entity\Etalonnage;
use App\Entity\Profil;
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
    const OPTION_CONCOURS = "concours";
    const OPTION_PROFIL = "profil";

    public function __construct(
        private readonly CorrecteurRepository $correcteurRepository
    )
    {
    }

    private function correcteurAndEtalonnageChoices(Concours|null $concours, Profil|null $profil): array
    {
        if ($concours != null) {
            $correcteurs = $this->correcteurRepository->findBy(["concours" => $concours]);
        } else if ($profil != null) {
            $correcteurs = $this->correcteurRepository->findBy(["profil" => $profil]);
        } else {
            $correcteurs = $this->correcteurRepository->findAll();
        }

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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $concours = $options[self::OPTION_CONCOURS] ?? null;
        $profil = $options[self::OPTION_PROFIL] ?? null;

        $builder->add("both", ChoiceType::class, [
            "choices" => $this->correcteurAndEtalonnageChoices($concours, $profil),
            "label" => "Correcteur et étalonnage"
        ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define(self::OPTION_CONCOURS);
        $resolver->define(self::OPTION_PROFIL);
        $resolver->setAllowedTypes(self::OPTION_CONCOURS, Concours::class);
        $resolver->setAllowedTypes(self::OPTION_PROFIL, Profil::class);
    }

}