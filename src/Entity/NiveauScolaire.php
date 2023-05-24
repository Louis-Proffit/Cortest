<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity]
#[UniqueEntity('nom')]
#[UniqueEntity('indice')]
class NiveauScolaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Positive]
    #[ORM\Column(unique: true)]
    public int $indice;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    /**
     * @param int $id
     * @param int $indice
     * @param string $nom
     */
    public function __construct(int $id, int $indice, string $nom)
    {
        $this->id = $id;
        $this->indice = $indice;
        $this->nom = $nom;
    }


}