<?php

namespace App\Entity;

use App\Repository\EpreuveNotationDirecteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveNotationDirecteRepository::class)]
class EpreuveNotationDirecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $numQuestion = null;

    #[ORM\Column]
    private ?float $repA = null;

    #[ORM\Column]
    private ?float $repB = null;

    #[ORM\Column]
    private ?float $repC = null;

    #[ORM\Column]
    private ?float $repD = null;

    #[ORM\Column]
    private ?float $repE = null;

    #[ORM\Column]
    private ?bool $boolA = null;

    #[ORM\Column]
    private ?bool $boolB = null;

    #[ORM\Column (nullable: true)]
    private ?bool $boolC = null;

    #[ORM\Column (nullable: true)]
    private ?bool $boolD = null;

    #[ORM\Column (nullable: true)]
    private ?bool $boolE = null;

    #[ORM\Column (nullable: true)]
    private ?float $noRep = null;

    #[ORM\Column(length: 50)]
    private ?string $intitule = null;

    #[ORM\Column(length: 10)]
    private ?string $abreviation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EpreuveVersion $version = null;

    public function __construct(EpreuveVersion $version, string $intitule = "", string $abreviation = "", int $numQuestion = 0, $repA = 0, $boolA = false, $repB = 0, $boolB = false, $repC = 0, $boolC = null, $repD = 0, $boolD = null, $repE = 0, $boolE = null, $noRep = 0) {
        $this->version = $version;
        $this->numQuestion = $numQuestion;
        $this->intitule = $intitule;
        $this->abreviation = $abreviation;
        $this->repA = $repA;
        $this->boolA = $boolA;
        $this->repB = $repB;
        $this->boolB = $boolB;
        $this->repC = $repC;
        $this->boolC = $boolC;
        $this->repD = $repD;
        $this->boolD = $boolD;
        $this->repE = $repE;
        $this->boolE = $boolE;
        $this->noRep = $noRep;
    }

    public function __toString()
    {
        echo 'Question ' . $this->numQuestion . ' : ' . $this->intitule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumQuestion(): ?int
    {
        return $this->numQuestion;
    }

    public function setNumQuestion(int $numQuestion): self
    {
        $this->numQuestion = $numQuestion;

        return $this;
    }

    public function getRepA(): ?float
    {
        return $this->repA;
    }

    public function setRepA(float $repA): self
    {
        $this->repA = $repA;

        return $this;
    }

    public function getRepB(): ?float
    {
        return $this->repB;
    }

    public function setRepB(float $repB): self
    {
        $this->repB = $repB;

        return $this;
    }

    public function getRepC(): ?float
    {
        return $this->repC;
    }

    public function setRepC(float $repC): self
    {
        $this->repC = $repC;

        return $this;
    }

    public function getRepD(): ?float
    {
        return $this->repD;
    }

    public function setRepD(float $repD): self
    {
        $this->repD = $repD;

        return $this;
    }

    public function getRepE(): ?float
    {
        return $this->repE;
    }

    public function setRepE(float $repE): self
    {
        $this->repE = $repE;

        return $this;
    }

    public function isBoolA(): ?bool
    {
        return $this->boolA;
    }

    public function setBoolA(bool $boolA): self
    {
        $this->boolA = $boolA;

        return $this;
    }

    public function isBoolB(): ?bool
    {
        return $this->boolB;
    }

    public function setBoolB(bool $boolB): self
    {
        $this->boolB = $boolB;

        return $this;
    }

    public function isBoolC(): ?bool
    {
        return $this->boolC;
    }

    public function setBoolC(bool $boolC): self
    {
        $this->boolC = $boolC;

        return $this;
    }

    public function isBoolD(): ?bool
    {
        return $this->boolD;
    }

    public function setBoolD(bool $boolD): self
    {
        $this->boolD = $boolD;

        return $this;
    }

    public function isBoolE(): ?bool
    {
        return $this->boolE;
    }

    public function setBoolE(bool $boolE): self
    {
        $this->boolE = $boolE;

        return $this;
    }

    public function getNoRep(): ?float
    {
        return $this->noRep;
    }

    public function setNoRep(float $noRep): self
    {
        $this->noRep = $noRep;

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
}
