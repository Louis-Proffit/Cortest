<?php

namespace App\Form;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

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
                    'label' => "Corriger pour la session",
                    'choices' => $this->sessionChoices()
                ]
            )
            ->add("Corriger", SubmitType::class);
    }
        
}