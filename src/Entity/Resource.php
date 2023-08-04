<?php

namespace App\Entity;

use App\Repository\ResourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Blameable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[UniqueEntity(fields: "nom", message: "Ce nom de resource existe déjà", errorPath: "nom")]
#[Gedmo\Loggable]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[Length(max: 256)]
    #[ORM\Column(length: 256)]
    public string $file_nom;

    #[ORM\ManyToOne(targetEntity: CortestUser::class)]
    #[Blameable(on: "create")]
    public CortestUser $user;

    /**
     * @param int $id
     * @param string $nom
     * @param string $file_nom
     */
    public function __construct(int $id, string $nom, string $file_nom)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->file_nom = $file_nom;
    }
}