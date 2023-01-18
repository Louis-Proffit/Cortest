<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[ORM\ManyToMany(targetEntity: Echelle::class)]
    public Collection $echelles;

    #[ORM\OneToMany(mappedBy: "profil", targetEntity: Etalonnage::class)]
    public Collection $etalonnages;

    #[ORM\OneToMany(mappedBy: "profil", targetEntity: Graphique::class)]
    public Collection $graphiques;

    /**
     * @param int $id
     * @param string $nom
     * @param Collection $echelles
     * @param Collection $etalonnages
     * @param Collection $graphiques
     */
    public function __construct(int $id, string $nom, Collection $echelles, Collection $etalonnages, Collection $graphiques)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->echelles = $echelles;
        $this->etalonnages = $etalonnages;
        $this->graphiques = $graphiques;
    }


}