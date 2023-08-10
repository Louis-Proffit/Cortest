<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity]
#[Gedmo\Loggable]
#[ORM\UniqueConstraint(fields: self::UNIQUE_FIELDS_ABREVIATION)]
#[ORM\UniqueConstraint(fields: self::UNIQUE_FIELDS_INDICE)]
#[UniqueEntity(fields: self::UNIQUE_FIELDS_ABREVIATION, message: "Cette abréviation existe déjà dans le même concours", errorPath: "abreviation")]
#[UniqueEntity(fields: self::UNIQUE_FIELDS_INDICE, message: "Cet indice existe déjà dans le même concours", errorPath: "indice")]
class QuestionTest
{
    const MAX_LEN_INTITULE = 50;
    const MAX_LEN_ABREVIATION = 10;

    const UNIQUE_FIELDS_ABREVIATION = ["test", "abreviation"];
    const UNIQUE_FIELDS_INDICE = ["test", "indice"];

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

    #[ORM\Column(length: self::MAX_LEN_INTITULE)]
    #[Length(max: self::MAX_LEN_INTITULE, maxMessage: "L'intitulé doit faire moins de " . self::MAX_LEN_ABREVIATION . "caractères")]
    public string $intitule;

    #[Length(max: self::MAX_LEN_ABREVIATION, maxMessage: "L'abréviation doit faire moins de " . self::MAX_LEN_ABREVIATION . "caractères")]
    #[ORM\Column(length: self::MAX_LEN_ABREVIATION)]
    public string $abreviation;

    #[ORM\ManyToOne(targetEntity: Test::class, cascade: ["persist"], inversedBy: "questions")]
    public Test $test;

    #[Choice(choices: self::TYPES)]
    #[ORM\Column]
    public string $type;

    /**
     * @param int $id
     * @param int $indice
     * @param string $intitule
     * @param string $abreviation
     * @param Test $test
     * @param string $type
     */
    public function __construct(int $id, int $indice, string $intitule, string $abreviation, Test $test, string $type)
    {
        $this->id = $id;
        $this->indice = $indice;
        $this->intitule = $intitule;
        $this->abreviation = $abreviation;
        $this->test = $test;
        $this->type = $type;
    }


    /*
     * TODO service ?
     * public static function initQuestions(GrilleRepository $grilleRepository, Concours $concours): void
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
    }*/
}