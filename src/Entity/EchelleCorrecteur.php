<?php

namespace App\Entity;

use App\Constraint\Compilable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Mapping\Annotation as Gedmo;

#[Gedmo\Loggable]
#[Entity]
class EchelleCorrecteur
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[Compilable]
    #[Gedmo\Versioned]
    #[ORM\Column(length: 10000)]
    public string $expression;

    #[ORM\ManyToOne(targetEntity: Echelle::class, inversedBy: "echelles_correcteur")]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Correcteur::class, inversedBy: "echelles")]
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