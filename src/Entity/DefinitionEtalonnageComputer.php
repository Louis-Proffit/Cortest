<?php

namespace App\Entity;

use App\Repository\EtalonnageComputerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtalonnageComputerRepository::class)]
class DefinitionEtalonnageComputer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: DefinitionScore::class)]
    public DefinitionScore $score;

    #[ORM\Column]
    public string $nom;

    #[ORM\Column]
    public string $nom_php;
}
