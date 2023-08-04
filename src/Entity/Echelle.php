<?php

namespace App\Entity;

use App\Constraint\PhpIdentifier;
use App\Repository\EchelleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Gedmo\Loggable]
#[Entity(repositoryClass: EchelleRepository::class)]
#[UniqueEntity(fields: self::FIELDS_UNIQUE_NOM_PHP, message: "Ce nom d'échelle php existe déjà pour cette structure", errorPath: "nom_php")]
#[UniqueEntity(fields: self::FIELDS_UNIQUE_NOM, message: "Ce nom d'échelle existe déjà pour cette structure", errorPath: "nom")]
#[ORM\UniqueConstraint(fields: self::FIELDS_UNIQUE_NOM_PHP)]
#[ORM\UniqueConstraint(fields: self::FIELDS_UNIQUE_NOM)]
class Echelle
{
    const FIELDS_UNIQUE_NOM_PHP = ["structure", "nom_php"];
    const FIELDS_UNIQUE_NOM = ["structure", "nom"];

    const TYPE_ECHELLE_SIMPLE = "Echelle simple";
    const TYPE_ECHELLE_COMPOSITE = "Echelle composite";

    const TYPE_ECHELLE_HIERARCHY = [
        self::TYPE_ECHELLE_SIMPLE => 0,
        self::TYPE_ECHELLE_COMPOSITE => 1
    ];

    const TYPE_ECHELLE_OPTIONS = [
        self::TYPE_ECHELLE_SIMPLE,
        self::TYPE_ECHELLE_COMPOSITE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column]
    public string $nom;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[PhpIdentifier]
    #[ORM\Column]
    public string $nom_php;

    #[Choice(choices: self::TYPE_ECHELLE_OPTIONS)]
    #[Gedmo\Versioned]
    #[ORM\Column]
    public string $type;

    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleCorrecteur::class, cascade: ["remove", "persist"])]
    public Collection $echelles_correcteur;

    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleEtalonnage::class, cascade: ["remove", "persist"])]
    public Collection $echelles_etalonnage;

    #[ORM\ManyToOne(targetEntity: Structure::class, inversedBy: "echelles")]
    public Structure $structure;

    /**
     * @param int $id
     * @param string $nom
     * @param string $nom_php
     * @param string $type
     * @param Collection $echelles_correcteur
     * @param Collection $echelles_etalonnage
     * @param Structure $structure
     */
    public function __construct(int $id, string $nom, string $nom_php, string $type, Collection $echelles_correcteur, Collection $echelles_etalonnage, Structure $structure)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->nom_php = $nom_php;
        $this->type = $type;
        $this->echelles_correcteur = $echelles_correcteur;
        $this->echelles_etalonnage = $echelles_etalonnage;
        $this->structure = $structure;
    }
}
