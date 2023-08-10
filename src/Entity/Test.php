<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use App\Repository\TestRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: TestRepository::class)]
#[Gedmo\Loggable()]
#[UniqueEntity(self::KEY_UNIQUE_NOM)]
class Test
{
    const KEY_UNIQUE_NOM = "nom";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[NotBlank]
    #[Gedmo\Versioned]
    #[ORM\Column]
    public string $version_batterie;

    #[Choice(choices: GrilleRepository::INDEX)]
    #[Gedmo\Versioned]
    #[ORM\Column]
    public int $index_grille;

    #[ORM\ManyToMany(targetEntity: Concours::class, inversedBy: "tests")]
    public Collection $concours;

    #[ORM\ManyToMany(targetEntity: Correcteur::class, mappedBy: "tests")]
    public Collection $correcteurs;

    #[ORM\OneToMany(mappedBy: "test", targetEntity: Session::class, cascade: ["persist"])]
    public Collection $sessions;

    #[ORM\OrderBy(["indice" => "ASC"])]
    #[ORM\OneToMany(mappedBy: "test", targetEntity: QuestionTest::class, cascade: ["persist", "remove"])]
    public Collection $questions;

    /**
     * @param int $id
     * @param string $nom
     * @param string $version_batterie
     * @param int $index_grille
     * @param Collection $concours
     * @param Collection $correcteurs
     * @param Collection $sessions
     * @param Collection $questions
     */
    public function __construct(int $id, string $nom, string $version_batterie, int $index_grille, Collection $concours, Collection $correcteurs, Collection $sessions, Collection $questions)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->version_batterie = $version_batterie;
        $this->index_grille = $index_grille;
        $this->concours = $concours;
        $this->correcteurs = $correcteurs;
        $this->sessions = $sessions;
        $this->questions = $questions;
    }

    public static function supprimable(Test $test): bool
    {
        return $test->sessions->isEmpty()
            && $test->correcteurs->isEmpty();
    }
}