<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadSessionBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("session_id",
                ChoiceType::class,
                [
                    'label' => "Session à corriger",
                    'choices' => array_combine(
                        array_map(fn(Session $session) => "Session du " . $session->date_saisie->format('Y-m-d H:i:s'),
                            $options["sessions"]),
                        array_map(fn(Session $session) => $session->id, $options["sessions"])
                    )
                ]
            )
            ->add(
                $builder->create("contents", FileType::class, [
                    // "mapped" => false,
                    "required" => true,
                    "constraints" => [
                        new File([
                            'maxSize' => '1024k',
                        ])
                    ],
                    'getter' => function (UploadSessionBase $upload, FormInterface $form) {
                        return null;
                    },
                    'setter' => function (UploadSessionBase &$upload, UploadedFile $state, FormInterface $form) {
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define("sessions");
        $resolver->setAllowedTypes("sessions", "array");
    }
}