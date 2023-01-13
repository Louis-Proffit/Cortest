<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
class EchelleEtalonnage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public array $bounds;

    #[ORM\ManyToOne(targetEntity: Echelle::class)]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Etalonnage::class)]
    public Etalonnage $etalonnage;


}