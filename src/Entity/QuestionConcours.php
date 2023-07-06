<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity]
#[ORM\UniqueConstraint(fields: self::UNIQUE_FIELDS_ABREVIATION)]
#[ORM\UniqueConstraint(fields: self::UNIQUE_FIELDS_INDICE)]
#[UniqueEntity(fields: self::UNIQUE_FIELDS_ABREVIATION, message: "Cette abréviation existe déjà dans le même concours", errorPath: "abreviation")]
#[UniqueEntity(fields: self::UNIQUE_FIELDS_INDICE, message: "Cet indice existe déjà dans le même concours", errorPath: "indice")]
class QuestionConcours
{
    const UNIQUE_FIELDS_ABREVIATION = ["concours", "abreviation"];
    const UNIQUE_FIELDS_INDICE = ["concours", "indice"];

    const TYPE_INUTILISE = "Inutilisé";
    const TYPE_VRAI_FAUX = "Vrai ou faux";
    const TYPE_SCORE = "Score";
    const TYPE_EXEMPLE = "Exemple";
    const TYPES = [self::TYPE_INUTILISE, self::TYPE_SCORE, self::TYPE_VRAI_FAUX, self::TYPE_EXEMPLE];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[Positive]
    #[ORM\Column]
    public int $indice;

    #[ORM\Column(length: 50)]
    public string $intitule;

    #[ORM\Column(length: 10)]
    public string $abreviation;

    #[ORM\ManyToOne(targetEntity: Concours::class, inversedBy: "questions")]
    public Concours $concours;

    #[Choice(choices: self::TYPES)]
    #[ORM\Column]
    public string $type;

    /**
     * @param int $id
     * @param int $indice
     * @param string $intitule
     * @param string $abreviation
     * @param Concours $concours
     * @param string $type
     */
    public function __construct(int $id, int $indice, string $intitule, string $abreviation, Concours $concours, string $type)
    {
        $this->id = $id;
        $this->indice = $indice;
        $this->intitule = $intitule;
        $this->abreviation = $abreviation;
        $this->concours = $concours;
        $this->type = $type;
    }


    public static function initQuestions(GrilleRepository $grilleRepository, Concours $concours): void
    {
        $grille = $grilleRepository->getFromIndex($concours->index_grille);

        for ($indice = 1; $indice <= $grille->nombre_questions; $indice++) {
            $concours->questions->add(
                new QuestionConcours(
                    id: 0,
                    indice: $indice,
                    intitule: "Q" . $indice,
                    abreviation: "Q" . $indice,
                    concours: $concours,
                    type: QuestionConcours::TYPE_INUTILISE
                )
            );
        }
    }
}