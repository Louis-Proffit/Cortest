<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity]
#[UniqueEntity('nom')]
class Concours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    #[ORM\OneToMany(mappedBy: "concours", targetEntity: Correcteur::class, cascade: ["remove", "persist"])]
    public Collection $correcteurs;

    #[ORM\OneToMany(mappedBy: "concours", targetEntity: Session::class, cascade: ["remove", "persist"])]
    public Collection $sessions;

    #[Choice(choices: GrilleRepository::INDEX)]
    #[ORM\Column]
    public int $index_grille;

    #[NotBlank]
    #[ORM\Column]
    public string $type_concours;

    #[NotBlank]
    #[ORM\Column]
    public string $version_batterie;

    #[ORM\OrderBy(["indice" => "ASC"])]
    #[ORM\OneToMany(mappedBy: "concours", targetEntity: QuestionConcours::class, cascade: ["persist", "remove"])]
    public Collection $questions;

    /**
     * @param int $id
     * @param string $nom
     * @param Collection $correcteurs
     * @param Collection $sessions
     * @param int $index_grille
     * @param string $type_concours
     * @param string $version_batterie
     * @param Collection $questions
     */
    public function __construct(int $id, string $nom, Collection $correcteurs, Collection $sessions, int $index_grille, string $type_concours, string $version_batterie, Collection $questions)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->correcteurs = $correcteurs;
        $this->sessions = $sessions;
        $this->index_grille = $index_grille;
        $this->type_concours = $type_concours;
        $this->version_batterie = $version_batterie;
        $this->questions = $questions;
    }


}