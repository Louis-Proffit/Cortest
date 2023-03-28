<?php

namespace App\Form;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ParametresLectureFichierCsvType extends AbstractType
{
    public function __construct(
        private readonly SessionRepository $session_repository
    ){}
    private function sessionDisplay(Session $session): string
    {
        return "Session : "
            . $session->date->format('Y-m-d')
            . " | "
            . $session->sgap->nom
            . " | "
            . $session->concours->nom;
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
                    'label' => "Importer pour la session",
                    'choices' => $this->sessionChoices()
                ]
            )
            ->add(
                $builder->create("contents", FileType::class, [
                    "required" => true,
                    "label" => "Fichier de notes CSV",
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'text/csv',
                                'application/csv',
                                'text/x-comma-separated-values',
                                'text/x-csv',
                                'text/plain',
                            ],
                            'mimeTypesMessage' => 'Veuillez choisir un fichier csv adéquat',
                            'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Le volume maximal accepté est {{ limit }} {{ suffix }}',
                        ])
                    ]
                ])
            )
            ->add("Corriger", SubmitType::class);
    }
}