<?php

namespace App\Entity;

use App\Constraint\ClassName;
use App\Repository\CorrecteurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: CorrecteurRepository::class)]
#[UniqueEntity('nom')]
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
    #[ORM\OneToMany(mappedBy: "correcteur", targetEntity: EchelleCorrecteur::class, cascade: ["remove", "persist"])]
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

    /**
     * Associe le nom des echelles php à leur type
     * @return string[]
     */
    public function get_echelle_types(): array
    {
        $result = [];

        /** @var EchelleCorrecteur $echelle */
        foreach ($this->echelles as $echelle) {
            $result[$echelle->echelle->nom_php] = $echelle->echelle->type;
        }

        return $result;
    }

    public function get_echelles_ids(): array
    {
        $echelles_ids = array();
        if (!$this->echelles->isEmpty()) {
            foreach ($this->echelles->getValues() as $echelle) {
                $echelles_ids[] = $echelle->id;
            }
        }
        return $echelles_ids;
    }


}
