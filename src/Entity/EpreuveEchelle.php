<?php

namespace App\Entity;

use App\Repository\EpreuveEchelleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveEchelleRepository::class)]
class EpreuveEchelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $numEchelle = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $typeComptabilisation = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $infoComplementaire = null;

    #[ORM\Column(length: 50)]
    private ?string $intitule = null;

    #[ORM\Column(length: 10)]
    private ?string $abreviation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EpreuveVersion $version = null;

    #[ORM\ManyToMany(targetEntity: EpreuveNotationDirecte::class)]
    private Collection $notationsDirectes;

    #[ORM\OneToMany(mappedBy: 'echelle', targetEntity: EpreuveClasse::class)]
    private Collection $classes;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $echelles;

    public function __construct(int $type, EpreuveVersion $version, int $numEchelle = null, string $intitule = null, string $abreviation = null, int $typeComptabilisation = null, string $infoComplementaire = null, ArrayCollection $notationsDirecte = null, ArrayCollection $echelles = null)
    {
        $this->type = $type;
        $this->version = $version;
        $this->numEchelle = $numEchelle;
        $this->intitule = $intitule;
        $this->abreviation = $abreviation;
        $this->typeComptabilisation = $typeComptabilisation;
        $this->infoComplementaire = $infoComplementaire;
        $this->notationsDirectes = new ArrayCollection();
        if ($notationsDirecte != null)
        {
            $this->notationsDirecte = $notationsDirecte;
        }
        $this->classes = new ArrayCollection();
        $this->echelles = new ArrayCollection();
        if ($echelles != null)
        {
            $this->echelles = $echelles;
        }
    }

    public static function constructSommeNote($type, $version, int $numEchelle, string $intitule, string $abreviation, array $notations = null, array $echelles = null)
    {
        return new EpreuveEchelle($type, $version, $numEchelle, intitule : $intitule, abreviation : $abreviation, typeComptabilisation : 0, notationsDirecte: $notations, echelles: $echelles);
    }

    public static function constructNombreVraiesQuestions($type, $version, int $numEchelle, string $intitule, string $abreviation, array $notations = null, array $echelles = null)
    {
        return new EpreuveEchelle($type, $version, $numEchelle, intitule : $intitule, abreviation : $abreviation, typeComptabilisation : 0, notationsDirecte: $notations, echelles: $echelles);
    }

    public static function constructNombreFaussesQuestions($type, $version, int $numEchelle, string $intitule, string $abreviation, array $notations = null, array $echelles = null)
    {
        return new EpreuveEchelle($type, $version, $numEchelle, intitule : $intitule, abreviation : $abreviation, typeComptabilisation : 0, notationsDirecte: $notations, echelles: $echelles);
    }

    public static function constructNombreQuestionsSpécifiques($type, $version, int $numEchelle, string $intitule, string $abreviation, array $notations = null, array $echelles = null)
    {
        return new EpreuveEchelle($type, $version, $numEchelle, intitule : $intitule, abreviation : $abreviation, typeComptabilisation : 0, notationsDirecte: $notations, echelles: $echelles);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeEpreuve(): ?int
    {
        return $this->codeEpreuve;
    }

    public function setCodeEpreuve(int $codeEpreuve): self
    {
        $this->codeEpreuve = $codeEpreuve;

        return $this;
    }

    public function getNumEchelle(): ?int
    {
        return $this->numEchelle;
    }

    public function setNumEchelle(int $numEchelle): self
    {
        $this->numEchelle = $numEchelle;

        return $this;
    }

    public function getTypeComptabilisation(): ?int
    {
        return $this->typeComptabilisation;
    }

    public function setTypeNotation(int $typeComptabilisation): self
    {
        $this->typeNotation = $typeComptabilisation;

        return $this;
    }

    public function getInfoComplementaire(): ?string
    {
        return $this->infoComplementaire;
    }

    public function setInfoComplementaire(?string $infoComplementaire): self
    {
        $this->infoComplementaire = $infoComplementaire;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getAbreviation(): ?string
    {
        return $this->abreviation;
    }

    public function setAbreviation(string $abreviation): self
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    public function getVersion(): ?EpreuveVersion
    {
        return $this->version;
    }

    public function setVersion(?EpreuveVersion $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection<int, EpreuveNotationDirecte>
     */
    public function getNotationsDirectes(): Collection
    {
        return $this->notationsDirectes;
    }

    public function addNotationsDirecte(EpreuveNotationDirecte $notationsDirecte): self
    {
        if (!$this->notationsDirectes->contains($notationsDirecte)) {
            $this->notationsDirectes->add($notationsDirecte);
        }

        return $this;
    }

    public function removeNotationsDirecte(EpreuveNotationDirecte $notationsDirecte): self
    {
        $this->notationsDirectes->removeElement($notationsDirecte);

        return $this;
    }

    /**
     * @return Collection<int, EpreuveClasse>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(EpreuveClasse $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setEchelle($this);
        }

        return $this;
    }

    public function removeClass(EpreuveClasse $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getEchelle() === $this) {
                $class->setEchelle(null);
            }
        }

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getEchelles(): Collection
    {
        return $this->echelles;
    }

    public function addEchelle(self $echelle): self
    {
        if (!$this->echelles->contains($echelle)) {
            $this->echelles->add($echelle);
        }

        return $this;
    }

    public function removeEchelle(self $echelle): self
    {
        $this->echelles->removeElement($echelle);

        return $this;
    }
}
