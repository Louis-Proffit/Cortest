<?php

namespace App\Entity;

use App\Constraint\Compilable;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
class EchelleCorrecteur
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[Compilable]
    #[ORM\Column]
    public string $expression;

    #[ORM\ManyToOne(targetEntity: Echelle::class)]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Correcteur::class)]
    public Correcteur $correcteur;

}