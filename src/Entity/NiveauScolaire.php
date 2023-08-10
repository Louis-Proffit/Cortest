<?php

namespace App\Entity;

use App\Repository\NiveauScolaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[Gedmo\Loggable]
#[ORM\Entity(repositoryClass: NiveauScolaireRepository::class)]
#[UniqueEntity('nom', message: "Ce nom de niveau scolaire existe déjà.")]
#[UniqueEntity('indice', message: "Cet indice de niveau scolaire existe déjà.")]
class NiveauScolaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Positive]
    #[Gedmo\Versioned]
    #[ORM\Column(unique: true)]
    public int $indice;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[ORM\OneToMany(mappedBy: "niveau_scolaire", targetEntity: ReponseCandidat::class)]
    public Collection $reponses_candidat;

    /**
     * @param int $id
     * @param int $indice
     * @param string $nom
     */
    public function __construct(int $id, int $indice, string $nom, Collection $reponses_candidat = new ArrayCollection())
    {
        $this->id = $id;
        $this->indice = $indice;
        $this->nom = $nom;
        $this->reponses_candidat = $reponses_candidat;
    }


    public static function supprimable(NiveauScolaire $niveauScolaire): bool
    {
        return $niveauScolaire->reponses_candidat->isEmpty();
    }
}