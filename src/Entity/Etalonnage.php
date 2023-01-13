<?php

namespace App\Entity;

use App\Repository\EtalonnageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: EtalonnageRepository::class)]
class Etalonnage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Profil::class)]
    public Profil $profil;

    #[ORM\Column]
    public string $nom;

    #[ORM\Column]
    public int $nombre_classes;

    #[ORM\OneToMany(mappedBy: "etalonnage", targetEntity: EchelleEtalonnage::class)]
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
