<?php

namespace App\Entity;

use App\Repository\EpreuveClasseCritereRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveClasseCritereRepository::class)]
class EpreuveClasseCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $caracteristique = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $relation = null;

    #[ORM\Column(length: 255)]
    private ?string $valeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurSup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaracteristique(): ?int
    {
        return $this->caracteristique;
    }

    public function setCaracteristique(int $caracteristique): self
    {
        $this->caracteristique = $caracteristique;

        return $this;
    }

    public function getRelation(): ?int
    {
        return $this->relation;
    }

    public function setRelation(int $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getValeurSup(): ?string
    {
        return $this->valeurSup;
    }

    public function setValeurSup(?string $valeurSup): self
    {
        $this->valeurSup = $valeurSup;

        return $this;
    }
}
