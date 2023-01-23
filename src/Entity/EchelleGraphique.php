<?php

namespace App\Entity;

use App\Constraint\IsGraphiqueEchelleOptions;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Choice;

#[Entity]
class EchelleGraphique
{
    const OPTION_NOM_AFFICHAGE = "Nom affiché";
    const OPTION_NOM_AFFICHAGE_PHP = "nom_affichage";

    const TYPE_ECHELLE_SIMPLE = "Echelle simple";
    const TYPE_ECHELLE_COMPOSITE = "Echelle composite";
    const TYPE_SUBTEST = "Subtest";
    const TYPE_EPREUVE = "Epreuve";

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

    #[IsGraphiqueEchelleOptions]
    #[ORM\Column]
    public array $options;

    #[Choice(choices: self::TYPE_ECHELLE_OPTIONS)]
    #[ORM\Column]
    public string $type;

    #[ORM\ManyToOne(targetEntity: Echelle::class)]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Graphique::class, inversedBy: "echelles")]
    public Graphique $graphique;

}