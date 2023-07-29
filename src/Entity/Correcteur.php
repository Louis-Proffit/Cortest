<?php

namespace App\Entity;

use App\Repository\CorrecteurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: CorrecteurRepository::class)]
#[UniqueEntity(fields: 'nom', message: "Ce nom de correcteur est déjà utilisé")]
class Correcteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToMany(targetEntity: Test::class, inversedBy: "correcteurs")]
    public Collection $tests;

    #[ORM\ManyToOne(targetEntity: Structure::class, inversedBy: "correcteurs")]
    public Structure $structure;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[Valid]
    #[ORM\OneToMany(mappedBy: "correcteur", targetEntity: EchelleCorrecteur::class, cascade: ["remove", "persist"])]
    public Collection $echelles;


    /**
     * @param int $id
     * @param Collection $tests
     * @param Structure $profil
     * @param string $nom
     * @param Collection $echelles
     */
    public function __construct(int $id, Collection $tests, Structure $profil, string $nom, Collection $echelles)
    {
        $this->id = $id;
        $this->tests = $tests;
        $this->structure = $profil;
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

    /**
     * TODO inutile ?
     * @return array
     */
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
