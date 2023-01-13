<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public string $nom;

    #[ORM\ManyToMany(targetEntity: Echelle::class)]
    public Collection $echelles;

    #[ORM\OneToMany(mappedBy: "profil", targetEntity: Etalonnage::class)]
    public Collection $etalonnages;

    /**
     * @param int $id
     * @param string $nom
     * @param Collection $echelles
     */
    public function __construct(int $id, string $nom, Collection $echelles)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->echelles = $echelles;
    }


}