<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Blameable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;


#[UniqueEntity(fields: "nom", message: "Ce nom de resource existe déjà", errorPath: "nom")]
#[ORM\Entity]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[ORM\Column]
    public string $file_nom;

    #[ORM\ManyToOne(targetEntity: CortestUser::class)]
    #[Blameable(on: "create")]
    public CortestUser $user;

    /**
     * @param int $id
     * @param string $nom
     * @param string $file_nom
     * @param CortestUser $user
     */
    public function __construct(int $id, string $nom, string $file_nom, CortestUser $user)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->file_nom = $file_nom;
        $this->user = $user;
    }
}