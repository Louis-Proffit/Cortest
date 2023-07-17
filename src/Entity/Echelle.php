<?php

namespace App\Entity;

use App\Constraint\PhpIdentifier;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[UniqueEntity(fields: self::FIELDS_UNIQUE_NOM_PHP, message: "Ce nom d'échelle php existe déjà pour ce profil", errorPath: "nom_php")]
#[UniqueEntity(fields: self::FIELDS_UNIQUE_NOM, message: "Ce nom d'échelle existe déjà pour ce profil", errorPath: "nom")]
#[ORM\UniqueConstraint(fields: self::FIELDS_UNIQUE_NOM_PHP)]
#[ORM\UniqueConstraint(fields: self::FIELDS_UNIQUE_NOM)]
class Echelle
{
    const FIELDS_UNIQUE_NOM_PHP = ["profil", "nom_php"];
    const FIELDS_UNIQUE_NOM = ["profil", "nom"];


    const TYPE_ECHELLE_SIMPLE = "Echelle simple";
    const TYPE_ECHELLE_COMPOSITE = "Echelle composite";
    const TYPE_SUBTEST = "Subtest";
    const TYPE_EPREUVE = "Epreuve";

    const TYPE_ECHELLE_HIERARCHY = [
        self::TYPE_ECHELLE_SIMPLE => 0,
        self::TYPE_ECHELLE_COMPOSITE => 1,
        self::TYPE_SUBTEST => 2,
        self::TYPE_EPREUVE => 3
    ];

    const TYPE_ECHELLE_OPTIONS = [
        self::TYPE_ECHELLE_SIMPLE,
        self::TYPE_ECHELLE_COMPOSITE,
        self::TYPE_SUBTEST,
        self::TYPE_EPREUVE
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column]
    public string $nom;

    #[NotBlank]
    #[PhpIdentifier]
    #[ORM\Column]
    public string $nom_php;

    #[Choice(choices: self::TYPE_ECHELLE_OPTIONS)]
    #[ORM\Column]
    public string $type;

    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleCorrecteur::class, cascade: ["remove", "persist"])]
    public Collection $echelles_correcteur;

    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleEtalonnage::class, cascade: ["remove", "persist"])]
    public Collection $echelles_etalonnage;


    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: "echelles")]
    public Profil $profil;

    /**
     * @param int $id
     * @param string $nom
     * @param string $nom_php
     * @param string $type
     * @param Collection $echelles_correcteur
     * @param Collection $echelles_etalonnage
     * @param Collection $echelles_graphiques
     * @param Profil $profil
     */
    public function __construct(int $id, string $nom, string $nom_php, string $type, Collection $echelles_correcteur, Collection $echelles_etalonnage, Collection $echelles_graphiques, Profil $profil)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->nom_php = $nom_php;
        $this->type = $type;
        $this->echelles_correcteur = $echelles_correcteur;
        $this->echelles_etalonnage = $echelles_etalonnage;
        $this->echelles_graphiques = $echelles_graphiques;
        $this->profil = $profil;
    }


}
