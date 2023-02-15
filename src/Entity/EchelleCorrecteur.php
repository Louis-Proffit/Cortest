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

    #[ORM\ManyToOne(targetEntity: Echelle::class, inversedBy: "echelles_correcteur")]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Correcteur::class)]
    public Correcteur $correcteur;

    /**
     * @param int $id
     * @param string $expression
     * @param Echelle $echelle
     * @param Correcteur $correcteur
     */
    public function __construct(int $id, string $expression, Echelle $echelle, Correcteur $correcteur)
    {
        $this->id = $id;
        $this->expression = $expression;
        $this->echelle = $echelle;
        $this->correcteur = $correcteur;
    }


}