<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[UniqueEntity('nom')]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[ORM\ManyToMany(targetEntity: Echelle::class, cascade: ["remove", "persist"])]
    public Collection $echelles;

    #[ORM\OneToMany(mappedBy: "profil", targetEntity: Correcteur::class, cascade: ["remove", "persist"])]
    public Collection $correcteurs;

    #[ORM\OneToMany(mappedBy: "profil", targetEntity: Etalonnage::class, cascade: ["remove", "persist"])]
    public Collection $etalonnages;

    #[ORM\OneToMany(mappedBy: "profil", targetEntity: Graphique::class, cascade: ["remove", "persist"])]
    public Collection $graphiques;

    /**
     * @param int $id
     * @param string $nom
     * @param Collection $echelles
     * @param Collection $correcteurs
     * @param Collection $etalonnages
     * @param Collection $graphiques
     */
    public function __construct(int $id, string $nom, Collection $echelles = new ArrayCollection(), Collection $correcteurs = new ArrayCollection(), Collection $etalonnages = new ArrayCollection(), Collection $graphiques = new ArrayCollection())
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->echelles = $echelles;
        $this->correcteurs = $correcteurs;
        $this->etalonnages = $etalonnages;
        $this->graphiques = $graphiques;
    }
}