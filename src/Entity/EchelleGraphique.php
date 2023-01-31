<?php

namespace App\Entity;

use App\Constraint\IsGraphiqueEchelleOptions;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
class EchelleGraphique
{
    const OPTION_NOM_AFFICHAGE = "Nom affiché";
    const OPTION_NOM_AFFICHAGE_PHP = "nom_affichage";
    const TYPE_ECHELLE_SIMPLE = 1;
    const TYPE_ECHELLE_OPTIONS = array(self::TYPE_ECHELLE_SIMPLE);

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column]
    public int $type;

    #[IsGraphiqueEchelleOptions]
    #[ORM\Column]
    public array $options;

    #[ORM\ManyToOne(targetEntity: Echelle::class)]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Graphique::class, inversedBy: "echelles")]
    public Graphique $graphique;

}