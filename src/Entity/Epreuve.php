<?php

namespace App\Entity;

use App\Repository\EpreuveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveRepository::class)]
class Epreuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etiquette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptif = null;

    #[ORM\OneToMany(mappedBy: 'epreuve', targetEntity: EpreuveVersion::class)]
    private Collection $versions;

    public function __construct(int $code = null, string $etiquette = null, string $descriptif = null) {
        $this->code = $code;
        $this->etiquette = $etiquette;
        $this->descriptif = $descriptif;
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getEtiquette(): ?string
    {
        return $this->etiquette;
    }

    public function setEtiquette(?string $etiquette): self
    {
        $this->etiquette = $etiquette;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * @return Collection<int, EpreuveVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    /**
     * @return EpreuveVersion the version idVersion in the versions linked to the epreuve
     */
    public function getVersion(int $idVersion): EpreuveVersion
    {
        for ($i = 0; $i < count($this->versions); ++$i)
        {
            if ($this->versions[$i]->getCodeVersion() == $idVersion)
            {
                return $this->versions[$i];
            }
        }

        return null;
    }

    public function addVersion(EpreuveVersion $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setEpreuve($this);
        }

        return $this;
    }

    public function removeVersion(EpreuveVersion $version): self
    {
        if ($this->versions->removeElement($version)) {
            // set the owning side to null (unless already changed)
            if ($version->getEpreuve() === $this) {
                $version->setEpreuve(null);
            }
        }

        return $this;
    }
}
