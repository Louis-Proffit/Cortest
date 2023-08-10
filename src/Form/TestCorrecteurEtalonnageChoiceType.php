<?php

namespace App\Form;

use App\Entity\Correcteur;
use App\Entity\Structure;
use App\Entity\Test;
use App\Form\Data\TestCorrecteurEtalonnageTriplet;
use App\Repository\CorrecteurRepository;
use App\Repository\StructureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestCorrecteurEtalonnageChoiceType extends AbstractType
{
    const OPTION_TEST = "test";
    const OPTION_STRUCTURE = "structure";

    public function __construct(
        private readonly CorrecteurRepository $correcteurRepository,
        private readonly StructureRepository  $structureRepository,
    )
    {
    }

    private function testCorrecteurEtalonnageChoices(Test|null $constraintTest, Structure|null $constraintStructure): array
    {
        if ($constraintStructure == null) {
            $structures = $this->structureRepository->findAll();
        } else {
            $structures = [$constraintStructure];
        }

        if ($constraintTest != null) {
            $correcteurs = $constraintTest->correcteurs->toArray();
        } else if ($constraintStructure != null) {
            $correcteurs = $constraintStructure->correcteurs->toArray();
        } else {
            $correcteurs = $this->correcteurRepository->findAll();
        }

        $result = [];
        foreach ($structures as $structure) {

            $sub_result = [];

            foreach ($structure->etalonnages as $etalonnage) {
                /** @var Correcteur $correcteur */
                foreach ($correcteurs as $correcteur) {

                    if ($constraintTest == null) {
                        foreach ($correcteur->tests as $test) {
                            $entity = new TestCorrecteurEtalonnageTriplet(
                                test: $test,
                                correcteur: $correcteur,
                                etalonnage: $etalonnage
                            );
                            $sub_result[$entity->summary()] = $entity;
                        }
                    } else {
                        $entity = new TestCorrecteurEtalonnageTriplet(
                            test: $constraintTest,
                            correcteur: $correcteur,
                            etalonnage: $etalonnage
                        );
                        $sub_result[$entity->summary()] = $entity;
                    }

                }
            }

            $result["Structure " . $structure->nom] = $sub_result;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $concours = $options[self::OPTION_TEST] ?? null;
        $profil = $options[self::OPTION_STRUCTURE] ?? null;

        $builder->add("value", ChoiceType::class, [
            "choices" => $this->testCorrecteurEtalonnageChoices($concours, $profil),
            "label" => "Test, correcteur et étalonnage"
        ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define(self::OPTION_TEST);
        $resolver->define(self::OPTION_STRUCTURE);
        $resolver->setAllowedTypes(self::OPTION_TEST, Test::class);
        $resolver->setAllowedTypes(self::OPTION_STRUCTURE, Structure::class);
    }

}