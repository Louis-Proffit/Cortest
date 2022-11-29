<?php

namespace App\Entity;

use App\Repository\EpreuveClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

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

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $valeurDroite = null;

    #[ORM\ManyToMany(targetEntity: EpreuveClasseCritere::class)]
    private Collection $criteres;

    public function __construct()
    {
        $this->criteres = new ArrayCollection();
    }
    
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

    public function getValeurDroite(): ?int
    {
        return $this->valeurDroite;
    }

    public function setValeurDroite(?int $valeurDroite): self
    {
        $this->valeurDroite = $valeurDroite;

        return $this;
    }

    /**
     * @return Collection<int, EpreuveClasseCritere>
     */
    public function getCriteres(): Collection
    {
        return $this->criteres;
    }

    public function addCritere(EpreuveClasseCritere $critere): self
    {
        if (!$this->criteres->contains($critere)) {
            $this->criteres->add($critere);
        }

        return $this;
    }

    public function removeCritere(EpreuveClasseCritere $critere): self
    {
        $this->criteres->removeElement($critere);

        return $this;
    }
}
