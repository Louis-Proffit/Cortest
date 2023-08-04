<?php

namespace App\Entity;

use App\Repository\GraphiqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;


#[Gedmo\Loggable]
#[UniqueEntity(fields: self::KEY_NOM_UNIQUE, message: "Ce nom de graphique existe déjà", errorPath: "nom")]
#[ORM\Entity(repositoryClass: GraphiqueRepository::class)]
class Graphique
{

    const KEY_NOM_UNIQUE = "nom";
    const MAX_FILE_SIZE = 1024 * 1024;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Structure::class, inversedBy: "graphiques")]
    public Structure $structure;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column]
    public string $file_nom;

    /**
     * @param int $id
     * @param Structure $structure
     * @param string $nom
     * @param string $file_nom
     */
    public function __construct(int $id, Structure $structure, string $nom, string $file_nom)
    {
        $this->id = $id;
        $this->structure = $structure;
        $this->nom = $nom;
        $this->file_nom = $file_nom;
    }
}