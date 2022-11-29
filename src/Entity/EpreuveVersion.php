<?php

namespace App\Entity;

use App\Repository\EpreuveVersionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveVersionRepository::class)]
class EpreuveVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $codeVersion = null;

    /*
     * 0 correspond à 'En cours de création', 1 à 'Activé', 2 à 'Désactivé' 
     */
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptif = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nom = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $prenom = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nomJeuneFille = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $niveauScolaire = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $naissance = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $sexe = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $concours = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $sgap = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $dateExamen = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $typeConcours = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $versionBatterie = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $reserve = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $champ1 = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $champ2 = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $codeBarre = null;

    #[ORM\ManyToOne(inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Epreuve $epreuve = null;

    public function __construct(Epreuve $epreuve = null, int $codeVersion = null, int $statut = null, $descriptif = null) {
        $this->epreuve = $epreuve;
        $this->codeVersion = $codeVersion;
        $this->statut = $statut;
        $this->descriptif = $descriptif;
        $this->nom = 0;
        $this->prenom = 0;
        $this->nomJeuneFille = 0;
        $this->niveauScolaire = 0;
        $this->naissance = 0;
        $this->sexe = 0;
        $this->concours = 0;
        $this->sgap = 0;
        $this->dateExamen = 0;
        $this->typeConcours = 0;
        $this->versionBatterie = 0;
        $this->reserve = 0;
        $this->champ1 = 0;
        $this->champ2 = 0;
        $this->codeBarre = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeVersion(): ?int
    {
        return $this->codeVersion;
    }

    public function setCodeVersion(int $codeVersion): self
    {
        $this->codeVersion = $codeVersion;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

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

    public function getNom(): ?int
    {
        return $this->nom;
    }

    public function setNom(int $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?int
    {
        return $this->prenom;
    }

    public function setPrenom(int $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNomJeuneFille(): ?int
    {
        return $this->nomJeuneFille;
    }

    public function setNomJeuneFille(int $nomJeuneFille): self
    {
        $this->nomJeuneFille = $nomJeuneFille;

        return $this;
    }

    public function getNiveauScolaire(): ?int
    {
        return $this->niveauScolaire;
    }

    public function setNiveauScolaire(int $niveauScolaire): self
    {
        $this->niveauScolaire = $niveauScolaire;

        return $this;
    }

    public function getNaissance(): ?int
    {
        return $this->naissance;
    }

    public function setNaissance(int $naissance): self
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getSexe(): ?int
    {
        return $this->sexe;
    }

    public function setSexe(int $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getConcours(): ?int
    {
        return $this->concours;
    }

    public function setConcours(int $concours): self
    {
        $this->concours = $concours;

        return $this;
    }

    public function getSgap(): ?int
    {
        return $this->sgap;
    }

    public function setSgap(int $sgap): self
    {
        $this->sgap = $sgap;

        return $this;
    }

    public function getDateExamen(): ?int
    {
        return $this->dateExamen;
    }

    public function setDateExamen(int $dateExamen): self
    {
        $this->dateExamen = $dateExamen;

        return $this;
    }

    public function getTypeConcours(): ?int
    {
        return $this->typeConcours;
    }

    public function setTypeConcours(int $typeConcours): self
    {
        $this->typeConcours = $typeConcours;

        return $this;
    }

    public function getVersionBatterie(): ?int
    {
        return $this->versionBatterie;
    }

    public function setVersionBatterie(int $versionBatterie): self
    {
        $this->versionBatterie = $versionBatterie;

        return $this;
    }

    public function getReserve(): ?int
    {
        return $this->reserve;
    }

    public function setReserve(int $reserve): self
    {
        $this->reserve = $reserve;

        return $this;
    }

    public function getChamp1(): ?int
    {
        return $this->champ1;
    }

    public function setChamp1(int $champ1): self
    {
        $this->champ1 = $champ1;

        return $this;
    }

    public function getChamp2(): ?int
    {
        return $this->champ2;
    }

    public function setChamp2(int $champ2): self
    {
        $this->champ2 = $champ2;

        return $this;
    }

    public function getCodeBarre(): ?int
    {
        return $this->codeBarre;
    }

    public function setCodeBarre(int $codeBarre): self
    {
        $this->codeBarre = $codeBarre;

        return $this;
    }

    public function getEpreuve(): ?Epreuve
    {
        return $this->epreuve;
    }

    public function setEpreuve(?Epreuve $epreuve): self
    {
        $this->epreuve = $epreuve;

        return $this;
    }

}
