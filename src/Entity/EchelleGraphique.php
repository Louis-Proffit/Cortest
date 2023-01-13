<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
class EchelleGraphique
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    // #[Compilable]
    #[ORM\Column]
    public string $displayName;

    #[ORM\ManyToOne(targetEntity: Echelle::class)]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Graphique::class)]
    public Graphique $graphique;

}