<?php

namespace App\Form;

use App\Entity\Etalonnage;
use App\Form\Data\CorrecteurChoice;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\Data\CorrecteurEtEtalonnagePair;
use App\Repository\CorrecteurRepository;
use App\Repository\EtalonnageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorrecteurEtEtalonnageChoiceType extends AbstractType
{
    const GRILLE_ID_OPTION = "grille_id";


    public function __construct(
        private readonly CorrecteurRepository $correcteur_repository,
        private readonly EtalonnageRepository $etalonnage_repository
    )
    {
    }

    private function correcteurAndEtalonnageChoices(int $grille_id): array
    {
        $correcteurs = $this->correcteur_repository->findBy(["grille_id" => $grille_id]);

        $result = [];
        foreach ($correcteurs as $correcteur) {

            $sub_result = [];

            $etalonnages = $this->etalonnage_repository->findBy(["score_id" => $correcteur->score_id]);

            foreach ($etalonnages as $etalonnage) {
                $sub_result[$etalonnage->nom] = new CorrecteurEtEtalonnagePair($correcteur, $etalonnage);
            }

            $result[$correcteur->nom] = $sub_result;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("correcteur_et_etalonnage", ChoiceType::class, [
            "choices" => $this->correcteurAndEtalonnageChoices($options["grille_id"])
        ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define(self::GRILLE_ID_OPTION);
        $resolver->setAllowedTypes(self::GRILLE_ID_OPTION, "int");
    }

}