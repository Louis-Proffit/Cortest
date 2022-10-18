<?php

namespace App\Entity;

use App\Repository\EpreuveClasseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveClasseRepository::class)]
class EpreuveClasse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EpreuveEchelle $echelle = null;

    #[ORM\Column]
    private ?float $limite = null;

    #[ORM\Column(nullable: true)]
    private ?float $valeurDroite = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEchelle(): ?EpreuveEchelle
    {
        return $this->echelle;
    }

    public function setEchelle(?EpreuveEchelle $echelle): self
    {
        $this->echelle = $echelle;

        return $this;
    }

    public function getLimite(): ?float
    {
        return $this->limite;
    }

    public function setLimite(float $limite): self
    {
        $this->limite = $limite;

        return $this;
    }

    public function getValeurDroite(): ?float
    {
        return $this->valeurDroite;
    }

    public function setValeurDroite(?float $valeurDroite): self
    {
        $this->valeurDroite = $valeurDroite;

        return $this;
    }
}
