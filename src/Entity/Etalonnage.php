<?php

namespace App\Entity;

use App\Repository\EtalonnageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;


#[ORM\Entity(repositoryClass: EtalonnageRepository::class)]
#[UniqueEntity('nom', message: "Ce nom d'étalonnage existe déjà")]
class Etalonnage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Structure::class, inversedBy: "etalonnages")]
    public Structure $structure;

    #[NotBlank]
    #[ORM\Column(name: 'nom', unique: true)]
    public string $nom;

    #[Positive]
    #[ORM\Column]
    public int $nombre_classes;

    #[ORM\OneToMany(mappedBy: "etalonnage", targetEntity: EchelleEtalonnage::class, cascade: ["remove", "persist"])]
    public Collection $echelles;

    /**
     * @param int $id
     * @param Structure $profil
     * @param string $nom
     * @param int $nombre_classes
     * @param Collection $echelles
     */
    public function __construct(int $id, Structure $profil, string $nom, int $nombre_classes, Collection $echelles)
    {
        $this->id = $id;
        $this->structure = $profil;
        $this->nom = $nom;
        $this->nombre_classes = $nombre_classes;
        $this->echelles = $echelles;
    }
}
