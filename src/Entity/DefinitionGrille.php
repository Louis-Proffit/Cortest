<?php

namespace App\Entity;

use App\Repository\DefinitionGrilleRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: DefinitionGrilleRepository::class)]
class DefinitionGrille
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
