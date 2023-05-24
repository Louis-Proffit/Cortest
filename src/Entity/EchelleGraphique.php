<?php

namespace App\Entity;

use App\Constraint\IsGraphiqueEchelleOptions;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class EchelleGraphique
{
    const OPTION_NOM_AFFICHAGE = "Nom affiché";
    const OPTION_NOM_AFFICHAGE_PHP = "nom_affichage";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[IsGraphiqueEchelleOptions]
    #[ORM\Column]
    public array $options;

    #[ORM\ManyToOne(targetEntity: Echelle::class, inversedBy: "echelles_graphiques")]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Graphique::class, inversedBy: "echelles")]
    public Graphique $graphique;

    /**
     * @param int $id
     * @param array $options
     * @param Echelle $echelle
     * @param Graphique $graphique
     */
    public function __construct(int $id, array $options, Echelle $echelle, Graphique $graphique)
    {
        $this->id = $id;
        $this->options = $options;
        $this->echelle = $echelle;
        $this->graphique = $graphique;
    }


}