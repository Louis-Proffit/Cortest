<?php

namespace App\Entity;

use App\Repository\EpreuveEchelleSimpleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveEchelleSimpleRepository::class)]
class EpreuveEchelleSimple
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $codeEpreuve = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $version = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $numEchelle = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $typeComptabilisation = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $questionAssociee = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $infoComplementaire = null;

    #[ORM\Column(length: 50)]
    private ?string $intitule = null;

    #[ORM\Column(length: 10)]
    private ?string $abreviation = null;

    public static function constructSommeNote(int $codeEpreuve, int $version, int $numEchelle, array $numQuestions)
    {
        $echelleSimple = array_fill(0, count($numQuestions), null);
        for ($i = 0; $i < count($numQuestions); ++$i)
            {
                $echelleSimple[$i] = new EpreuveEchelleSimple($codeEpreuve, $version, $numEchelle, 0, $numQuestions[$i]);
            }
        return $echelleSimple;
    }

    public static function constructNombreVraiesQuestions(int $codeEpreuve, int $version, int $numEchelle, array $numQuestions)
    {
        $echelleSimple = array_fill(0, count($numQuestions), null);
        for ($i = 0; $i < count($numQuestions); ++$i)
            {
                $echelleSimple[$i] = new EpreuveEchelleSimple($codeEpreuve, $version, $numEchelle, 1, $numQuestions[$i]);
            }
        return $echelleSimple;
    }

    public static function constructNombreFaussesQuestions(int $codeEpreuve, int $version, int $numEchelle, array $numQuestions)
    {
        $echelleSimple = array_fill(0, count($numQuestions), null);
        for ($i = 0; $i < count($numQuestions); ++$i)
            {
                $echelleSimple[$i] = new EpreuveEchelleSimple($codeEpreuve, $version, $numEchelle, 2, $numQuestions[$i]);
            }
        return $echelleSimple;
    }

    public static function constructNombreQuestionsSpécifiques(int $codeEpreuve, int $version, int $numEchelle, array $numQuestions, array $valQuestions)
    {
        $echelleSimple = array_fill(0, count($numQuestions), null);
        for ($i = 0; $i < count($numQuestions); ++$i)
            {
                $echelleSimple[$i] = new EpreuveEchelleSimple($codeEpreuve, $version, $numEchelle, 3, $numQuestions[$i], $valQuestions[$i]);
            }
        return $echelleSimple;
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

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVerion(int $version): self
    {
        $this->verion = $version;

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

    public function getQuestionAssociee(): ?int
    {
        return $this->questionAssociee;
    }

    public function setQuestionAssociee(int $questionAssociee): self
    {
        $this->questionAssociee = $questionAssociee;

        return $this;
    }

    public function getInfoComplementaire(): ?int
    {
        return $this->infoComplementaire;
    }

    public function setInfoComplementaire(?int $infoComplementaire): self
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
}
