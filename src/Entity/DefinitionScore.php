<?php

namespace App\Entity;

use App\Repository\DefinitionScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefinitionScoreRepository::class)]
class DefinitionScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public string $nom;

    #[ORM\Column]
    public string $nom_php;
}
