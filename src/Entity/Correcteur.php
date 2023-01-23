<?php

namespace App\Entity;

use App\Constraint\ClassName;
use App\Repository\CorrecteurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: CorrecteurRepository::class)]
class Correcteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ClassName]
    #[ORM\Column]
    public string $grille_class;

    #[ORM\ManyToOne(targetEntity: Profil::class)]
    public Profil $profil;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[Valid]
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
