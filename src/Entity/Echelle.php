<?php

namespace App\Entity;

use App\Constraint\PhpIdentifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[UniqueEntity('nom')]
#[UniqueEntity('nom_php')]
class Echelle
{

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
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[PhpIdentifier]
    #[ORM\Column(unique: true)]
    public string $nom_php;

    #[Choice(choices: self::TYPE_ECHELLE_OPTIONS)]
    #[ORM\Column]
    public string $type;

    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleCorrecteur::class, cascade: ["remove", "persist"])]
    public Collection $echelles_correcteur;


    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleEtalonnage::class, cascade: ["remove", "persist"])]
    public Collection $echelles_etalonnage;

    #[ORM\OneToMany(mappedBy: "echelle", targetEntity: EchelleGraphique::class, cascade: ["remove", "persist"])]
    public Collection $echelles_graphiques;

    /**
     * @param int $id
     * @param string $nom
     * @param string $nom_php
     * @param string $type
     * @param Collection $echelles_correcteur
     * @param Collection $echelles_etalonnage
     */
    public function __construct(int $id, string $nom, string $nom_php, string $type, Collection $echelles_correcteur = new ArrayCollection(), Collection $echelles_etalonnage = new ArrayCollection())
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->nom_php = $nom_php;
        $this->type = $type;
        $this->echelles_correcteur = $echelles_correcteur;
        $this->echelles_etalonnage = $echelles_etalonnage;
    }


}
