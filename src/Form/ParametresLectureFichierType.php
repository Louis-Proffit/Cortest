<?php

namespace App\Form;

use App\Entity\Session;
use App\Form\Data\ParametresLectureJSON;
use App\Repository\SessionRepository;
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
        private readonly SessionRepository $session_repository
    )
    {
    }

    private function sessionDisplay(Session $session): string
    {
        return "Session : "
            . $session->date->format('Y-m-d')
            . " | "
            . $session->sgap->nom
            . " | "
            . $session->test->nom;
    }

    private function sessionChoices(): array
    {
        $sessions = $this->session_repository->findAll();
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
                    "label" => "Fichier de notes JSON",
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'application/json',
                            ],
                            'mimeTypesMessage' => 'Importer un fichier .json valide',
                        ])
                    ],
                    'getter' => function (ParametresLectureJSON $upload, FormInterface $form) {
                        return null;
                    },
                    'setter' => function (ParametresLectureJSON $upload, UploadedFile $state, FormInterface $form) {
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