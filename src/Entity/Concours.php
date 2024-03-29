<?php

namespace App\Entity;

use App\Repository\ConcoursRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ConcoursRepository::class)]
#[ORM\UniqueConstraint(fields: self::KEY_NOM_TYPE_UNIQUE)]
#[UniqueEntity(fields: self::KEY_NOM_TYPE_UNIQUE, message: "Cette combinaison (intitulé/type concours) existe déjà.")]
class Concours
{

    const KEY_NOM_TYPE_UNIQUE = ["intitule", "type_concours"];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column]
    public string $intitule;

    #[NotBlank]
    #[ORM\Column]
    public string $type_concours;

    #[ORM\ManyToMany(targetEntity: Test::class, mappedBy: "concours")]
    public Collection $tests;

    #[ORM\OneToMany(mappedBy: "concours", targetEntity: Session::class, cascade: ["persist"])]
    public Collection $sessions;

    /**
     * @param int $id
     * @param string $nom
     * @param string $type_concours
     * @param Collection $tests
     */
    public function __construct(int $id, string $nom, string $type_concours, Collection $tests, Collection $sessions)
    {
        $this->id = $id;
        $this->intitule = $nom;
        $this->type_concours = $type_concours;
        $this->tests = $tests;
        $this->sessions = $sessions;
    }

    public function summary(): string
    {
        return $this->intitule . " (" . $this->type_concours . ")";
    }

    public static function supprimable(Concours $concours): bool
    {
        return $concours->tests->isEmpty();
    }
}