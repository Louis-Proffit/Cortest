<?php

namespace App\Entity;

use App\Repository\DefinitionScoreComputerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefinitionScoreComputerRepository::class)]
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
    public string $nom;

    #[ORM\Column]
    public string $nom_php;
}
