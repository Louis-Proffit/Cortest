<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;


#[ORM\Entity]
#[UniqueEntity(fields: ["nom", "graphique"])]
class Subtest
{
    const TYPE_SUBTEST_BR_MR = 0;
    const TYPE_SUBTEST_COMPOSITE = 1;
    const TYPE_SUBTEST = [self::TYPE_SUBTEST_BR_MR, self::TYPE_SUBTEST_COMPOSITE];
    const TYPES_SUBTEST_CHOICES = array("Subtest de BR/MR" => self::TYPE_SUBTEST_BR_MR, "Subtest d'échelles composites" => self::TYPE_SUBTEST_COMPOSITE);

    const TYPE_FOOTER_SCORE_AND_CLASSE = 0;
    const TYPE_FOOTER_SCORE_ONLY = 1;
    const TYPE_FOOTER_POURCENT = 2;
    const TYPES_FOOTERS = [self::TYPE_FOOTER_SCORE_AND_CLASSE, self::TYPE_FOOTER_SCORE_ONLY, self::TYPE_FOOTER_POURCENT];
    const TYPES_FOOTER_CHOICES = array("Score et classe" => self::TYPE_FOOTER_SCORE_AND_CLASSE, "Score uniquement" => self::TYPE_FOOTER_SCORE_ONLY, "Pourcentage (en complément d'un score)" => self::TYPE_FOOTER_POURCENT);

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[ORM\Column]
    public string $nom;

    #[Choice(choices: self::TYPE_SUBTEST)]
    #[ORM\Column]
    public int $type;

    /**
     * Si le subtest est de type BR/MR, c'est un array de couples (echelle_br_id, echelle_mr_id)
     * Sinon, c'est un array de couples (echelle_composite_id, (echelle_simple_id_1, ..., echelle_simple_id_n))
     * @var array
     */
    #[ORM\Column(length: 4096)]
    public array $echelles_core;

    /**
     * Array de couples (echelle_id, type_footer)
     * @var int[]
     */
    #[ORM\Column(length: 100)]
    public array $echelles_footer;

    #[ORM\ManyToOne(targetEntity: Graphique::class, inversedBy: "subtests")]
    public Graphique $graphique;

    /**
     * @param int $id
     * @param string $nom
     * @param int $type
     * @param array $echelles_core
     * @param array $echelles_bas_de_cadre
     * @param Graphique $graphique
     */
    public function __construct(int $id, string $nom, int $type, array $echelles_core, array $echelles_bas_de_cadre, Graphique $graphique)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->type = $type;
        $this->echelles_core = $echelles_core;
        $this->echelles_footer = $echelles_bas_de_cadre;
        $this->graphique = $graphique;
    }


}