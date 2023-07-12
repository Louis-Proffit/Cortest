<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\NotBlank;


#[UniqueEntity(fields: "nom", message: "Ce nom de graphique existe déjà", errorPath: "nom")]
#[ORM\Entity]
class Graphique
{

    const MAX_FILE_SIZE = 1024 * 1024;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: "graphiques")]
    public Profil $profil;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    // TODO syntax validation
    #[Orm\Column(length: self::MAX_FILE_SIZE)]
    public string $content;

    /**
     * @param int $id
     * @param Profil $profil
     * @param string $nom
     * @param string $content
     */
    public function __construct(int $id, Profil $profil, string $nom, string $content)
    {
        $this->id = $id;
        $this->profil = $profil;
        $this->nom = $nom;
        $this->content = $content;
    }
}