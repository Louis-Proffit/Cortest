<?php

namespace App\Entity;

use App\Repository\StructureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Gedmo\Loggable]
#[Entity(repositoryClass: StructureRepository::class)]
#[UniqueEntity('nom')]
class Structure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[ORM\OneToMany(mappedBy: "structure", targetEntity: Echelle::class, cascade: ["remove", "persist"])]
    public Collection $echelles;

    #[ORM\OneToMany(mappedBy: "structure", targetEntity: Correcteur::class, cascade: ["persist"])]
    public Collection $correcteurs;

    #[ORM\OneToMany(mappedBy: "structure", targetEntity: Etalonnage::class, cascade: ["persist"])]
    public Collection $etalonnages;

    #[ORM\OneToMany(mappedBy: "structure", targetEntity: Graphique::class, cascade: ["persist"])]
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

    public static function supprimable(Structure $structure): bool
    {
        return $structure->correcteurs->isEmpty()
            && $structure->etalonnages->isEmpty()
            && $structure->graphiques->isEmpty();
    }
}