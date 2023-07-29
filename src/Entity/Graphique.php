<?php

namespace App\Entity;

use App\Repository\GraphiqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;


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
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[ORM\Column]
    public string $file_nom;

    /**
     * @param int $id
     * @param Structure $profil
     * @param string $nom
     * @param string $file_nom
     */
    public function __construct(int $id, Structure $profil, string $nom, string $file_nom)
    {
        $this->id = $id;
        $this->structure = $profil;
        $this->nom = $nom;
        $this->file_nom = $file_nom;
    }
}