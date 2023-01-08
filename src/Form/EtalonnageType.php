<?php

namespace App\Form;

use App\Entity\DefinitionProfilComputer;
use App\Form\Data\ParametresLectureFichier;
use App\Repository\DefinitionScoreRepository;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class EtalonnageType extends AbstractType
{


    public function __construct(
        private readonly DefinitionScoreRepository $definition_score_repository
    )
    {
    }

    private function getDefinitionScoreOptions(): array
    {
        $definitions_score = $this->definition_score_repository->findAll();

        $result = [];

        foreach ($definitions_score as $definition_score) {
            $result[$definition_score->nom] = $definition_score;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "nom",
            TextType::class
        )->add(
            "score",
            ChoiceType::class,
            [
                "choices" => $this->getDefinitionScoreOptions()
            ]
        )->add(
            "file",
            FileType::class,
            [
                "required" => true,
                "label" => "Fichier de calcul",
                'getter' => function (DefinitionProfilComputer $state, FormInterface $form) {
                    return null;
                },
                'setter' => function (DefinitionProfilComputer $state, UploadedFile $upload, FormInterface $form) {
                    $nom_file = $upload->getClientOriginalName();
                    $state->nom_php = basename($nom_file, ".php");
                }
            ])->add("submit", SubmitType::class, ["label" => "Créer"]);
    }

}