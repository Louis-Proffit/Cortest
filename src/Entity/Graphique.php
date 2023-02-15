<?php

namespace App\Entity;

use App\Constraint\IsGraphiqueOptions;
use App\Core\Renderer\Renderer;
use App\Core\Renderer\RendererRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;


#[ORM\Entity]
#[UniqueEntity('nom')]
class Graphique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[IsGraphiqueOptions]
    #[ORM\Column]
    public array $options;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: "graphiques")]
    public Profil $profil;

    #[ORM\OneToMany(mappedBy: "graphique", targetEntity: EchelleGraphique::class, cascade: ["remove", "persist"])]
    public Collection $echelles;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[Choice(choices: RendererRepository::INDEX)]
    #[ORM\Column]
    public int $renderer_index;

    /**
     * @param int $id
     * @param array $options
     * @param Profil $profil
     * @param Collection $echelles
     * @param string $nom
     * @param int $renderer_index
     */
    public function __construct(int $id, array $options, Profil $profil, Collection $echelles, string $nom, int $renderer_index)
    {
        $this->id = $id;
        $this->options = $options;
        $this->profil = $profil;
        $this->echelles = $echelles;
        $this->nom = $nom;
        $this->renderer_index = $renderer_index;
    }

    public function getTypeEchelle(): array
    {
        $typeEchelle = array();
        foreach ($this->echelles as $echelle) {
            $typeEchelle[$echelle->echelle->nom_php] = $echelle->echelle->type;
        }
        return $typeEchelle;
    }

    public static function initializeEchelles(Graphique $graphique, Renderer $renderer): void
    {
        foreach ($graphique->profil->echelles as $echelle) {
            $graphique->echelles->add(new EchelleGraphique(
                id: 0, options: $renderer->initializeEchelleOption($echelle), echelle: $echelle, graphique: $graphique
            ));
        }
    }
}