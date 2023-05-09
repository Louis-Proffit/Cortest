<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportCorrecteurType extends AbstractType
{
    const FILE_KEY = "file";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::FILE_KEY, FileType::class, [
            'label' => 'Fichier de correction',
            "mapped" => false,
            "required" => true,
            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => ['application/xml', 'text/xml'],
                    'mimeTypesMessage' => 'Veuillez importer un fichier XML valide',
                ])
            ],
        ])->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}