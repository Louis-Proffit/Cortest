<?php

namespace App\Form;

use App\Constants\Sgaps;
use App\Entity\Session;
use App\Form\Data\ParametresLectureFichier;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class ParametresLectureFichierType extends AbstractType
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly Sgaps           $sgaps,
    )
    {
    }

    private function sessionDisplay(Session $session): string
    {
        return "Session : "
            . $session->date->format('Y-m-d H:i:s')
            . " | "
            . $this->sgaps->nom($session->sgap_index);
    }

    private function sessionChoices(): array
    {
        /** @var Session[] $sessions */
        $sessions = $this->doctrine->getRepository(Session::class)->findAll();
        $result = [];

        foreach ($sessions as $session) {
            $result[$this->sessionDisplay($session)] = $session;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("session",
                ChoiceType::class,
                [
                    'label' => "Corriger pour la session",
                    'choices' => $this->sessionChoices()
                ]
            )
            ->add(
                $builder->create("contents", FileType::class, [
                    // "mapped" => false,
                    "required" => true,
                    "label" => "Fichier de notes",
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'application/json',
                            ],
                            'mimeTypesMessage' => 'Importer un fichier .json valide',
                        ])
                    ],
                    'getter' => function (ParametresLectureFichier $upload, FormInterface $form) {
                        return null;
                    },
                    'setter' => function (ParametresLectureFichier $upload, UploadedFile $state, FormInterface $form) {
                        $upload->contents = "";
                        $handle = $state->openFile();
                        while (!$handle->eof()) {
                            $upload->contents = $upload->contents . $handle->getCurrentLine();
                        }
                    }
                ])
            )
            ->add("Corriger", SubmitType::class);
    }
}