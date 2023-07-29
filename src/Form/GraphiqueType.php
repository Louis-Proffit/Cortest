<?php

namespace App\Form;

use App\Entity\Graphique;
use App\Repository\StructureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class GraphiqueType extends AbstractType
{

    const ALLOWED_FILE_EXTENSION = "twig";
    const FILE_KEY = "file";

    public function __construct(
        private readonly StructureRepository $profilRepository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add("nom", TextType::class)
            ->add("profil", ChoiceType::class, [
                "choices" => $this->profilRepository->choices()
            ])
            ->add(self::FILE_KEY, FileType::class, [
                "label" => "Fichier de contenu",
                "getter" => function (Graphique $graphique, FormInterface $form) {
                    return null;
                },
                "setter" => function (Graphique $graphique, UploadedFile $uploadedFile, FormInterface $form) {
                    $graphique->file_nom = $uploadedFile->getClientOriginalName();
                },
                "constraints" => [
                    new File([
                        "maxSize" => "" . Graphique::MAX_FILE_SIZE,
                        "extensions" => [self::ALLOWED_FILE_EXTENSION => ["text/*", "application/*"]],
                    ])
                ],
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }
}