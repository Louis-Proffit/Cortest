<?php

namespace App\Entity;

use App\Repository\SgapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[ORM\Entity(repositoryClass: SgapRepository::class)]
#[UniqueEntity('indice', message: "Cet indice existe déjà.")]
#[UniqueEntity('nom', message: "Ce nom existe déjà.")]
class Sgap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[PositiveOrZero]
    #[ORM\Column(unique: true)]
    public int $indice;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[ORM\OneToMany(mappedBy: "sgap", targetEntity: Session::class, cascade: ["persist"])]
    public Collection $sessions;

    /**
     * @param int $id
     * @param int $indice
     * @param string $nom
     * @param Collection $sessions
     */
    public function __construct(int $id, int $indice, string $nom, Collection $sessions = new ArrayCollection())
    {
        $this->id = $id;
        $this->indice = $indice;
        $this->nom = $nom;
        $this->sessions = $sessions;
    }

    public static function supprimable(Sgap $sgap): bool
    {
        return $sgap->sessions->isEmpty();
    }

    public static function affichage(Sgap $sgap): string
    {
        return $sgap->indice . " - " . $sgap->nom ;
    }

}