<?php

namespace App\Entity;

use App\Repository\CorrecteurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorrecteurRepository::class)]
class Correcteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public string $grille_class;

    #[ORM\ManyToOne(targetEntity: Profil::class)]
    public Profil $profil;

    #[ORM\Column]
    public string $nom;

    #[ORM\OneToMany(mappedBy: "correcteur", targetEntity: EchelleCorrecteur::class)]
    public Collection $echelles;

    /**
     * @param int $id
     * @param string $grille_class
     * @param Profil $profil
     * @param string $nom
     * @param Collection $echelles
     */
    public function __construct(int $id, string $grille_class, Profil $profil, string $nom, Collection $echelles)
    {
        $this->id = $id;
        $this->grille_class = $grille_class;
        $this->profil = $profil;
        $this->nom = $nom;
        $this->echelles = $echelles;
    }


}
