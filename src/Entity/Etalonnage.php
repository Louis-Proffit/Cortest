<?php

namespace App\Entity;

use App\Repository\EtalonnageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;


#[ORM\Entity(repositoryClass: EtalonnageRepository::class)]
#[UniqueEntity('nom')]
class Etalonnage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: "etalonnages")]
    public Profil $profil;

    #[NotBlank]
    #[ORM\Column(name:'nom', unique: true)]
    public string $nom;

    #[Positive]
    #[ORM\Column]
    public int $nombre_classes;

    #[ORM\OneToMany(mappedBy: "etalonnage", targetEntity: EchelleEtalonnage::class, cascade: ["remove", "persist"])]
    public Collection $echelles;

    /**
     * @param int $id
     * @param Profil $profil
     * @param string $nom
     * @param int $nombre_classes
     * @param Collection $echelles
     */
    public function __construct(int $id, Profil $profil, string $nom, int $nombre_classes, Collection $echelles)
    {
        $this->id = $id;
        $this->profil = $profil;
        $this->nom = $nom;
        $this->nombre_classes = $nombre_classes;
        $this->echelles = $echelles;
    }
}
