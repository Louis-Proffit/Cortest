<?php

namespace App\Entity;

use App\Constraint\PhpIdentifier;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
class Echelle
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[PhpIdentifier]
    #[ORM\Column(unique: true)]
    public string $nom_php;

    /**
     * @param int $id
     * @param string $nom
     * @param string $nom_php
     */
    public function __construct(int $id, string $nom, string $nom_php)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->nom_php = $nom_php;
    }


}