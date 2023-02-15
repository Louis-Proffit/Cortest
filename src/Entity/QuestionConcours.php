<?php

namespace App\Entity;

use App\Constraint\ClassName;
use App\Repository\GrilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity]
class QuestionConcours
{
    const TYPE_INUTILISE = "Inutilisé";
    const TYPE_VRAI_FAUX = "Vrai ou faux";
    const TYPE_SCORE = "Score";
    const TYPES = [self::TYPE_INUTILISE, self::TYPE_SCORE, self::TYPE_VRAI_FAUX];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[Positive]
    #[ORM\Column]
    public int $indice;

    #[ORM\ManyToOne(targetEntity: Concours::class, inversedBy: "questions")]
    public Concours $concours;

    #[Choice(choices: self::TYPES)]
    #[ORM\Column]
    public string $type;

    /**
     * @param int $id
     * @param int $indice
     * @param Concours $concours
     * @param string $type
     */
    public function __construct(int $id, int $indice, Concours $concours, string $type)
    {
        $this->id = $id;
        $this->indice = $indice;
        $this->concours = $concours;
        $this->type = $type;
    }

    public static function initQuestions(GrilleRepository $grille_repository, Concours $concours): void
    {
        $grille = $grille_repository->getFromIndex($concours->index_grille);

        for ($indice = 1; $indice <= $grille->nombre_questions; $indice++) {
            $concours->questions->add(
                new QuestionConcours(
                    id: 0,
                    indice: $indice,
                    concours: $concours,
                    type: QuestionConcours::TYPE_INUTILISE
                )
            );
        }
    }
}