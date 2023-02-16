<?php

namespace App\Form;

use App\Entity\Session;
use App\Form\Data\ParametresLectureJSON;
use App\Repository\SessionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class ParametresLectureOptiqueType extends AbstractType
{
    public function __construct(
        private readonly SessionRepository $session_repository
    )
    {
    }

    private function sessionDisplay(Session $session): string
    {
        return "Session : "
            . $session->date->format('d-m-Y')
            . " | "
            . $session->sgap->nom;
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
            ->add("Corriger", SubmitType::class);
    }
        
}