<?php

namespace App\Form;

use App\Entity\Graphique;
use App\Entity\Resource;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class ResourceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add("nom", TextType::class)
            ->add("file", FileType::class, [
                "label" => "Fichier de contenu",
                "getter" => function (Resource $resource, FormInterface $form) {
                    return null;
                },
                "setter" => function (Resource $resource, UploadedFile $file, FormInterface $form) {
                    var_dump($file->getClientOriginalName());
                    $resource->file_nom = $file->getClientOriginalName();
                }])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }
}