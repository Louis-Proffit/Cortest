<?php

namespace App\Entity;

use App\Repository\ScoreComputerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreComputerRepository::class)]
class DefinitionScoreComputer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: DefinitionGrille::class)]
    public DefinitionGrille $grille;

    #[ORM\ManyToOne(targetEntity: DefinitionScore::class)]
    public DefinitionScore $score;

    #[ORM\Column]
    public $nom;

    #[ORM\Column]
    public $nom_php;
}
